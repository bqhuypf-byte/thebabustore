<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariant;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class ProductContent
{
    /** Render old content without changing the stored original or its image references. */
    public static function forProduct(Product $product, ?ProductVariant $variant = null): array
    {
        [$description, $legacyRows] = self::legacy((string) $product->description);
        $groups = [];
        $seen = [];
        $withheld = [];
        foreach ($product->specification_groups ?? [] as $group) {
            $rows = [];
            foreach ($group['rows'] ?? [] as $row) {
                if (($row['verification_status'] ?? 'verified') === 'pending') {
                    $key = self::key($row['label'] ?? '');
                    $seen[$key] = $withheld[$key] = true;
                    continue;
                }
                if (trim($row['label'] ?? '') === '' || trim($row['value'] ?? '') === '') continue;
                $rows[] = $row;
                $seen[self::key($row['label'])] = true;
            }
            if ($rows) $groups[] = ['title' => $group['title'] ?? 'Thông số kỹ thuật', 'rows' => $rows];
        }
        $legacyRows = $product->content_standardized_at ? [] : array_values(array_filter($legacyRows, fn ($row) => !isset($seen[self::key($row['label'])])));
        foreach (collect($legacyRows)->groupBy(fn ($row) => $row['group'] ?? 'Thông số sản phẩm') as $title => $rows) {
            $groups[] = ['title' => $title, 'rows' => $rows->values()->all()];
        }
        $overrides = array_filter($variant?->specifications ?? [], fn ($label) => !isset($withheld[self::key((string) $label)]), ARRAY_FILTER_USE_KEY);
        $remaining = $overrides;
        foreach ($groups as &$group) {
            foreach ($group['rows'] as &$row) {
                foreach ($overrides as $label => $value) {
                    if (self::key($label) === self::key($row['label'])) {
                        $row['value'] = $value;
                        unset($remaining[$label]);
                    }
                }
            }
            unset($row);
        }
        unset($group);
        if ($remaining) {
            $groups[] = ['title' => 'Thông số phiên bản', 'rows' => collect($remaining)->map(fn ($value, $label) => ['label' => $label, 'value' => $value])->values()->all()];
        }
        return ['description' => $description, 'groups' => $groups];
    }

    private static function key(string $value): string
    {
        return Str::lower(trim(Str::ascii($value)));
    }

    public static function sanitize(?string $html): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="content-root">'.($html ?? '').'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('content-root');
        if (!$root) return '';
        self::clean($root);
        $output = '';
        foreach ($root->childNodes as $node) $output .= $doc->saveHTML($node);
        return $output;
    }

    /** Split editable warranty HTML into compact accordion sections without changing stored content. */
    public static function warrantySections(?string $html): array
    {
        $sanitized = self::sanitize($html);
        if (trim(strip_tags($sanitized)) === '') return [];

        $doc = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="warranty-root">'.$sanitized.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('warranty-root');
        if (!$root) return [];

        $intro = '';
        $current = null;
        $sections = [];
        foreach (iterator_to_array($root->childNodes) as $node) {
            if ($node instanceof DOMElement && $node->tagName === 'h2') {
                if ($current) $sections[] = $current;
                $rawTitle = trim($node->textContent);
                $cleanTitle = trim((string) preg_replace('/^(?:[IVXLCDM]+|\d+)[\.\)\s-]+/iu', '', $rawTitle));
                $current = ['title' => $cleanTitle !== '' ? $cleanTitle : $rawTitle, 'html' => ''];
                continue;
            }
            $fragment = $doc->saveHTML($node);
            if ($current) $current['html'] .= $fragment;
            else $intro .= $fragment;
        }
        if ($current) $sections[] = $current;

        if (!$sections) return [['title' => 'Quy trình và chính sách cửa hàng', 'html' => $sanitized]];
        if (count($sections) > 1 && self::key($sections[0]['title']) === 'chinh sach bao hanh') {
            array_shift($sections);
        }
        return array_values($sections);
    }

    public static function legacy(string $html): array
    {
        // Older Trix imports contain escaped attribute quotes and entire articles in headings.
        $html = str_replace('\"', '"', $html);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="product-content-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('product-content-root');
        if (!$root) return ['', []];
        foreach (iterator_to_array($root->getElementsByTagName('*')) as $node) {
            if (!preg_match('/^h[1-6]$/', $node->tagName)) continue;
            if (!$node->getElementsByTagName('img')->length && !$node->getElementsByTagName('br')->length && mb_strlen($node->textContent) < 250) continue;
            $wrapper = $doc->createElement('div');
            while ($node->firstChild) $wrapper->appendChild($node->firstChild);
            $node->parentNode?->replaceChild($wrapper, $node);
            $first = $wrapper->firstChild;
            if ($first instanceof DOMElement && $first->tagName === 'strong' && mb_strlen($first->textContent) < 200) {
                $heading = $doc->createElement('h3');
                while ($first->firstChild) $heading->appendChild($first->firstChild);
                $wrapper->replaceChild($heading, $first);
            }
        }
        $rows = [];
        foreach (iterator_to_array($root->getElementsByTagName('*')) as $heading) {
            if (!in_array($heading->tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) continue;
            if (!preg_match('/^[\s*⚙]*(?:thong so(?: k[y i] thuat)?|thong tin k[y i] thuat)\s*[:：]?$/', self::key($heading->textContent))) continue;
            $next = $heading->nextSibling;
            $consumed = [];
            $extracted = [];
            while ($next) {
                if (!$next instanceof DOMElement && trim($next->textContent) === '') { $next = $next->nextSibling; continue; }
                $parsed = self::parseRows($next);
                if (!$parsed) break;
                $extracted = array_merge($extracted, $parsed);
                $consumed[] = $next;
                $next = $next->nextSibling;
            }
            if ($extracted) {
                $rows = array_merge($rows, $extracted);
                foreach ($consumed as $node) $node->parentNode->removeChild($node);
                $heading->parentNode?->removeChild($heading);
            }
        }
        // Some older products describe several models, each with a short run of technical rows.
        // Move only unambiguous technical labels; retain marketing prose and the model context.
        $technicalLabels = ['driver', 'tro khang', 'do nhay', 'dai tan', 'tan so dap ung', 'ket noi', 'layout', 'pin', 'pcb', 'keycap', 'led', 'kich thuoc', 'trong luong'];
        $context = null;
        foreach (iterator_to_array($root->childNodes) as $node) {
            if (!$node instanceof DOMElement || !$node->parentNode) continue;
            $text = trim($node->textContent);
            if (in_array($node->tagName, ['h2', 'h3', 'h4'], true) || ($node->tagName === 'p' && mb_strlen($text) >= 5 && mb_strlen($text) <= 100 && mb_strtoupper($text) === $text && mb_strtolower($text) !== $text)) {
                $context = $text;
                continue;
            }
            if (!$context) continue;
            $cursor = $node;
            $cluster = [];
            $nodes = [];
            while ($cursor) {
                if (!$cursor instanceof DOMElement && trim($cursor->textContent) === '') { $cursor = $cursor->nextSibling; continue; }
                $parsed = self::parseRows($cursor);
                if (!$parsed || count(array_filter($parsed, fn ($row) => in_array(self::key($row['label']), $technicalLabels, true))) !== count($parsed)) break;
                foreach ($parsed as $row) $cluster[] = $row + ['group' => $context];
                $nodes[] = $cursor;
                $cursor = $cursor->nextSibling;
            }
            if (count($cluster) < 2) continue;
            $rows = array_merge($rows, $cluster);
            foreach ($nodes as $consumed) $consumed->parentNode->removeChild($consumed);
        }

        // Older imports use pipes, mixed lists, or omit the specifications heading entirely.
        $labels = ['model','cau truc','vo + phim','mau sac','ket noi','led','lcd screen','switches','switch','loai switch','polling rate','hot swappable','hot-swap','hotswap','n-key rollover','keycaps','keycap','trinh dieu khien','pin','dung luong pin','kich thuoc','kich thuoc ban phim (l x w x h)','kich thuoc (l x w x h)','kich thuoc ban phim','trong luong','can nang','phu kien','phu kien kem theo','chat lieu','xuat xu','do ben phim','thoi gian su dung sau khi sac day pin','thoi gian su dung khi sac day pin','den nen','hieu ung am thanh khi go phim','so luong phim','hanh trinh phim','dien ap/dong sac','dien ap dinh muc','dong dien dinh muc','giao dien sac','toc do phan hoi cua ban phim','dinh muc dong dien','he dieu hanh tuong thich','phan mem','tuong thich','layout','rgb','driver','tro khang','do nhay','dai tan','tan so dap ung','pcb','profile','so nut','ten san pham','bao hanh','thoi luong pin','panel','custom switch','3 kieu ket noi','foam','huong mach','type','operating force','total travel','pre-travel','luc nhan','luc kich hoat','luc cham day','do ben','dpi','cam bien','tan so phan hoi','chat lieu keycap'];
        foreach (iterator_to_array($root->getElementsByTagName('*')) as $node) {
            if (!$node->parentNode || !in_array($node->tagName, ['p','li','table'])) continue;
            if ($node->getElementsByTagName('img')->length || ($node->tagName !== 'table' && $node->getElementsByTagName('li')->length)) continue;
            if ($node->tagName === 'table') {
                $parsed = self::parseRows($node);
                if ($parsed && count($parsed) >= 2 && count(array_filter($parsed,fn($r)=>in_array(self::key($r['label']),$labels))) >= 2) {
                    $rows=array_merge($rows,$parsed);
                    $node->parentNode->removeChild($node);
                }
                continue;
            }
            $inner='';
            foreach ($node->childNodes as $child) $inner.=$doc->saveHTML($child);
            $pieces=preg_split('/<br\b[^>]*>/i',$inner);
            $keep=[]; $found=false;
            foreach ($pieces as $piece) {
                $line=trim(html_entity_decode(strip_tags($piece),ENT_QUOTES|ENT_HTML5,'UTF-8'));
                if (preg_match('/^[-–•\s]*([^:：|]{1,80})\s*[:：|]\s*\|?\s*(.+)$/u',$line,$match) && in_array(self::key(trim($match[1])),$labels,true) && mb_strlen($match[2])<800) {
                    $rows[]=['label'=>trim($match[1]),'value'=>trim($match[2])];
                    $found=true;
                } else $keep[]=$piece;
            }
            if (!$found) continue;
            $remainder=implode('<br>',$keep);
            if (trim(strip_tags($remainder))==='') $node->parentNode->removeChild($node);
            else {
                $fragmentDoc=new DOMDocument('1.0','UTF-8');
                $old=libxml_use_internal_errors(true);
                $fragmentDoc->loadHTML('<?xml encoding="UTF-8"><div id="remaining">'.$remainder.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);
                libxml_clear_errors();libxml_use_internal_errors($old);
                while($node->firstChild) $node->removeChild($node->firstChild);
                foreach($fragmentDoc->getElementById('remaining')->childNodes as $child) $node->appendChild($doc->importNode($child,true));
            }
        }
        // Turn rich-editor visual headings into semantic headings before removing pasted styles.
        foreach (iterator_to_array($root->getElementsByTagName('p')) as $paragraph) {
            $text = trim(str_replace("\u{00A0}", ' ', $paragraph->textContent));
            if ($text === '' && !$paragraph->getElementsByTagName('img')->length) {
                $paragraph->parentNode?->removeChild($paragraph);
                continue;
            }
            $headingSize = 0;
            foreach ($paragraph->getElementsByTagName('*') as $child) {
                if (trim($child->textContent) === trim($paragraph->textContent) && preg_match('/font-size:\s*(\d+)px/i', $child->getAttribute('style'), $match)) $headingSize = max($headingSize, (int) $match[1]);
            }
            $allCaps = mb_strlen($text) >= 5 && mb_strlen($text) <= 120 && mb_strtoupper($text) === $text && mb_strtolower($text) !== $text;
            $boldText = '';
            foreach ($paragraph->getElementsByTagName('strong') as $strong) $boldText .= $strong->textContent;
            $textKey = trim(preg_replace('/^[^\p{L}\p{N}]+/u','', $text));
            $boldKey = trim(preg_replace('/^[^\p{L}\p{N}]+/u','', $boldText));
            $boldHeading = mb_strlen($textKey)>=5 && mb_strlen($textKey)<=120 && $textKey === $boldKey && !preg_match('/[:：]\s*\S/u',$textKey) && !$paragraph->getElementsByTagName('a')->length;
            if (($headingSize >= 18 || $allCaps || $boldHeading) && !$paragraph->getElementsByTagName('img')->length && (!$paragraph->getElementsByTagName('br')->length || $boldHeading)) {
                $heading = $doc->createElement($headingSize >= 22 ? 'h2' : 'h3');
                while ($paragraph->firstChild) $heading->appendChild($paragraph->firstChild);
                $paragraph->parentNode?->replaceChild($heading, $paragraph);
            }
        }
        self::clean($root);
        $output = '';
        foreach ($root->childNodes as $node) $output .= $doc->saveHTML($node);
        return [$output, $rows];
    }

    private static function parseRows(DOMNode $node): array
    {
        if (!$node instanceof DOMElement || !in_array($node->tagName, ['p', 'ul', 'ol', 'table'], true)) return [];
        if ($node->getElementsByTagName('img')->length) return [];
        $lines = [];
        if ($node->tagName === 'table') {
            foreach ($node->getElementsByTagName('tr') as $tr) {
                $cells = array_values(array_filter(iterator_to_array($tr->childNodes), fn ($n) => $n instanceof DOMElement && in_array($n->tagName, ['td', 'th'])));
                if (count($cells) !== 2) return [];
                $lines[] = trim($cells[0]->textContent).': '.trim($cells[1]->textContent);
            }
        } else {
            $html = $node->ownerDocument->saveHTML($node);
            $html = preg_replace('/<br\b[^>]*>|<\/li>/i', "\n", $html);
            $lines = preg_split('/\R/u', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        $rows = [];
        foreach ($lines as $line) {
            $line = trim(str_replace("\u{00A0}", ' ', $line));
            if ($line === '') continue;
            if (!preg_match('/^[-–•\s]*([^:：|]{1,80})[:：|]\s*\|?\s*(.+)$/u', $line, $match)) return [];
            $rows[] = ['label' => trim($match[1]), 'value' => trim($match[2])];
        }
        return $rows;
    }

    private static function clean(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (!$node instanceof DOMElement) continue;
            if (in_array(strtolower($node->tagName), ['h1','h4','h5','h6'], true)) {
                $replacement = $node->ownerDocument->createElement($node->tagName === 'h1' ? 'h2' : 'h3');
                while ($node->firstChild) $replacement->appendChild($node->firstChild);
                $parent->replaceChild($replacement, $node);
                $node = $replacement;
            }
            if (in_array(strtolower($node->tagName), ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'svg', 'link', 'meta'], true)) {
                $parent->removeChild($node);
                continue;
            }
            foreach (iterator_to_array($node->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                $allowed = in_array($name, ['href', 'src', 'alt', 'title', 'colspan', 'rowspan', 'loading'], true);
                $safeRaster = $node->tagName === 'img' && $name === 'src' && preg_match('/^data:image\/(?:png|jpe?g|gif|webp);base64,[a-z0-9+\/=\s]+$/i', $attribute->value);
                if (!$allowed || (!$safeRaster && in_array($name, ['href', 'src'], true) && preg_match('/^(?:javascript|data|vbscript):/i', preg_replace('/[\x00-\x20]+/', '', $attribute->value)))) {
                    $node->removeAttribute($attribute->name);
                }
            }
            self::clean($node);
        }
    }
}

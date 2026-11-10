<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($products as $product)
    <url><loc>{{ url('/product/'.$product->slug) }}</loc>@if($product->updated_at)<lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>@endif</url>
@endforeach
</urlset>

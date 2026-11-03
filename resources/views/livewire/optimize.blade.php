<div>
    <ul>
        @foreach ($slides as $slide)
        <a href="{{Storage::url($slide->image) }}" target="_blank">
            <img style="width: 10px; height: 10px;" src="{{Storage::url($slide->image) }}" alt="">
        </a>
        @endforeach
        <br>
        @foreach ($blogs as $blog)
        <a href="{{Storage::url($blog->banner) }}" target="_blank">
            <img style="width: 10px; height: 10px;" src="{{Storage::url($blog->banner) }}" alt="">
        </a>
        @endforeach
        <br>
        @foreach ($products as $product)
        @foreach ($product->images as $image)
        <a href="{{Storage::url($image) }}" target="_blank">
            <img style="width: 10px; height: 10px;" src="{{Storage::url($image) }}" alt="">
        </a>
        @endforeach
        @endforeach
        <br>
        @foreach ($productVariants as $productVariant)
        <a href="{{Storage::url($productVariant->image) }}" target="_blank">
            <img style="width: 10px; height: 10px;" src="{{Storage::url($productVariant->image) }}" alt="">
        </a>
        @endforeach
        <br>
        @foreach ($products as $product)
        {!! $product->description !!}
        @endforeach
        <br>
        @foreach ($blogs as $blog)
        {!! $blog->description !!}
        @endforeach
    </ul>
    <style>
        img {
    width: 10px;
    height: 10px;
}
    </style>
</div>

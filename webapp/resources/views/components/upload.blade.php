@props(['upload'])

@if($upload->type === 'image')
    <img src="{{ $upload->url }}" />
@else
<video controls>
    <source src="{{ $upload->url}}" type="{{ $upload->mime}}">
    Your browser does not support the video tag.
  </video>
@endif

{$image = [
    'image_x' => $icon_width|default:30,
    'image_y' => $icon_height|default:30,
    'alt' => $icon_alt|default:''
]}

{if $addons.ab__video_gallery.video_icon == "youtube"}
    {$image["image_path"] = $icon_image|default:"`$images_dir`/addons/ab__video_gallery/youtube-icon.png"}
    {$image["absolute_path"] = $icon_image|default:"`$config.dir.root`/images/addons/ab__video_gallery/youtube-icon.png"}
    {$image["relative_path"] = $icon_image|default:"addons/ab__video_gallery/youtube-icon.png"}
{else}
    {$image["image_path"] = $icon_image|default:"`$images_dir`/addons/ab__video_gallery/video-icon.png"}
    {$image["absolute_path"] = $icon_image|default:"`$config.dir.root`/images/addons/ab__video_gallery/video-icon.png"}
    {$image["relative_path"] = $icon_image|default:"addons/ab__video_gallery/video-icon.png"}
{/if}

{include file="common/image.tpl" images=$image class=$icon_class|default:"ab-vg-video-icon" image_width=$image.image_x image_height=$image.image_y show_detailed_link=false}
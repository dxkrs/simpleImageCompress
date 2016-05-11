<?php
/**
* 
*/
class compress
{
    private $maxWidth; 
    private $maxHeight; 
    function __construct($maxWidth,$maxHeight)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }
    /**
     * 得到等比例缩放的长宽
     */
    function getNewSize($maxWidth, $maxHeight, $srcWidth, $srcHeight) {
        if ($srcWidth < $srcHeight) {
            $maxWidth = ($maxHeight / $srcHeight) * $srcWidth;
        } else {
            $maxHeight = ($maxWidth / $srcWidth) * $srcHeight;
        }
        return array('width' => $maxWidth,'height' => $maxHeight);
    }
    /**
     * 等比例生成缩略图
     *
     * @param  String  $srcFile  原始文件路径
     * @param  String  $dstFile  目标文件路径
     * @param  Integer  $maxWidth  生成的目标文件的最大宽度
     * @param  Integer  $maxHeight  生成的目标文件的最大高度
     * @return  Boolean  生成成功则返回true，否则返回false
     */
    function makeThumb($srcFile, $dstFile) {
        
        $maxWidth = $this->maxWidth;
        $maxHeight = $this->maxHeight;

        $isJpeg = false;
        $isGif = false;
        $isPng = false;
        if ($size = getimagesize($srcFile)) {
            $srcWidth = $size[0];
            $srcHeight = $size[1];
            $mime = $size['mime'];
            switch ($mime) {
                case 'image/jpeg';
                    $isJpeg = true;
                    break;
                case 'image/gif';
                    $isGif = true;
                    break;
                case 'image/png';
                    $isPng = true;
                    break;
                default:
                    return false;
                    break;
            }
            //header("Content-type:$mime");
            $arr =  $this->getNewSize($maxWidth, $maxHeight, $srcWidth, $srcHeight);
            $thumbWidth = $arr['width'];
            $thumbHeight = $arr['height'];
            if ($isJpeg) {
                $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
                $srcPic = imagecreatefromjpeg($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                //imagecopyresized($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagejpeg($dstThumbPic, $dstFile.'.jpg', 80);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
            } elseif ($isGif) {
                //$dstThumbPic = imagecreate($thumbWidth, $thumbHeight);  /* attention */
                $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);  /* attention */
                //创建透明画布
                imagealphablending($dstThumbPic, true);
                imagesavealpha($dstThumbPic, true);
                $trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
                imagefill($dstThumbPic, 0, 0, $trans_colour);

                $srcPic = imagecreatefromgif($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                //imagecopyresized($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagegif($dstThumbPic, $dstFile.'.gif',80);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
            } elseif ($isPng) {
                $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
                //创建透明画布
                imagealphablending($dstThumbPic, true);
                imagesavealpha($dstThumbPic, true);
                $trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
                imagefill($dstThumbPic, 0, 0, $trans_colour);

                $srcPic = imagecreatefrompng($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                //imagecopyresized($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagepng($dstThumbPic, $dstFile.'.png',80);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
// $compress = new compress("640", "320");//长和宽
// $compress->makeThumb("oldsize.jpg", 'newsize');//文件地址，新文件名
?>

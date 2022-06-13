<?php
/**
 * This class is intented to display a barcode as a image, for printing in business documents.
 * The basic functionality allows to create a image (PNG, GIF or JPEG) from a given text string.
 * By default this class uses a free Code39 Font to generate a barcode. It's possible to use any font that you want, feel free to parametrize.
 * @package barcode
 * @author H4
 * @license LGPL
 * @version 20091105220800
 */

class barcode{
    /**
     * This section allows you to modify the default font and errors messages.
     */
    const ERROR_SAVE_JPEG = "NO se puede crear el Archivo JPG ";
    const ERROR_SAVE_GIF = "NO se puede crear el Archivo GIF ";
    const ERROR_SAVE_PNG = "NO se puede crear el Archivo PNG ";
    const ERROR_SAVE_BAD_TYPE = "Tipo de Imágen incorrecto.";
    const DEFAULT_FONT_FAMILY = "/fonts/free3of9.ttf";
    
    protected $font_family;
    protected $font_width_relation;
    protected $extension;
    protected $image_resource;
    protected $error;
    protected $type;
    protected $path;

    /**
     * Instantiates a barcode class, that generate a image barcode from a given string, using a TTF font.
     * @param string $type Defines the image type or format. Valid values PNG, GIF and JPEG. Default PNG.
     * @param integer $resolution Only for JPEG. Defines the image resolution. For the others formats, has no effects.
     * @param bool|string $font_family If false, then the class uses the default font especified at barcode::DEFAULT_FONT_FAMILY. If a valid string that match a font path (may be relative), uses the parametrized font.
     * @param float $relation Relation between image width and font size width. Only used if "barcode::image_create fontsize" is false.
     */
    public function __construct($type="png",$resolution=72,$font_family=false,$relation=0.13){
        $this->path = pathinfo(__FILE__);
        $this->type = strtolower($type);
        $this->resolution = $resolution;
        $this->font_family = !$font_family ? $this->path["dirname"].barcode::DEFAULT_FONT_FAMILY : $font_family;
        $this->font_width_relation = $relation;
    }

    /**
     * Returns a timestamp with micro seconds for serialize the file names.
     * @return string 
     */
    private function serial(){
        return "-".strftime("%Y%m%d%H%M%S",time()).microtime(true);
    }

    /**
     * Set the Response Headers. Limited only for the Images Content-Type.
     * @return bool True if the headers were set. Otherwise false. 
     */
    private function set_headers(){
            return header("Content-type: image/".$this->type);
    }

    /**
     * This method creates the output image resource. After that, you can show() or save_file().
     * @param string $text The text string to encoded.
     * @param array $size The image size as an array(width,height).
     * @param float $angle The angle for the barcode. Useful for vertical (90 or 270) barcode.
     * @param float $fontsize The font size to use to encode the text string. If not set, the class uses the param $relation of a constructor.
     * @param array $text_color The font (barcode) color as an array(Red,Green,Blue).
     * @param array $fill_color The background color as an array(Red,Green,Blue).
     * @return bool True if the image was created. Otherwise False.
     */
    public function image_create($text="", $size = array(240,24), $angle=0, $fontsize=false, $text_color=array(0,0,0), $fill_color=array(255,255,255)){

        $this->error = false;

        $width = (int) $size[0];
        $height = (int) $size[1];

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image,$fill_color[0],$fill_color[1],$fill_color[2]);
        imagefill($image,0,0,$background);
        imagetruecolortopalette($image,false,2);

        !$fontsize ? $fontsize = (int) $width * $this->font_width_relation : null;
        $bounding_box = imagettfbbox($fontsize, $angle, $this->font_family, $text);
        $text_width = strlen($text)*$fontsize;

        /*
        Centers the barcode at background
        */
        $top = (int)($height-($bounding_box[5]-$bounding_box[1]))/2;
        $left = (int)($width-($bounding_box[4]-$bounding_box[0]))/2;

        $color = imagecolorallocate($image,$text_color[0],$text_color[1],$text_color[2]);

        imagettftext($image, $fontsize, $angle, $left, $top, $color, $this->font_family, $text);

        $this->image_resource = $image;

        return $this->error == false;
    }

    /**
     * This method saves a image resource to a file. It's posible to select a image type, that can differs from the $type at contructor parametrized.
     * @param bool|string $path If false, or not especified, then the file name is automaticaly generated.
     * @param string $type The image type that will save.
     */
    public function save_file($path=false, $type="png"){
        $this->error = false;
        !$path ? $path = "/barcode".$this->serial().".".$type : null;
        $type = strtolower($type);
        switch ($type){
            case "jpeg":
                if (!imagejpeg($this->image_resource, $path)) $this->error = barcode::ERROR_SAVE_JPEG.$path;
            break;

            case "gif":
                if (!imagegif($this->image_resource, $path)) $this->error = barcode::ERROR_SAVE_GIF.$path;
            break;

            case "png":
                if (!imagepng($this->image_resource, $path)) $this->error = barcode::ERROR_SAVE_PNG.$path;
            break;
            default:
                $this->error = barcode::ERROR_SAVE_BAD_TYPE;
            break;
        }

    }

    /**
     * This method show in a browser the image generated by barcode::image_create().
     */
    public function show(){
        $this->set_headers($this->type);
        ob_start();
        eval("image".$this->type."(\$this->image_resource);");
        $out = ob_get_contents();
        ob_end_clean();
        $this->type=="jpeg"?$out = substr_replace($out, pack('cnn',1,$this->resolution,$this->resolution),13,5):null;
        print $out;
    }

    /**
     * At script's finish, the image resource is destroyed.
     * However you can call this method at any time, if necessary.
     */
    public function __destroy(){
        @imagedestroy($this->image_resource);
    }
}
?>

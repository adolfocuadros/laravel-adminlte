<?php
namespace App\Lib;

use Intervention\Image\ImageManagerStatic as Image;
use Ramsey\Uuid\Uuid;

class FileUpload
{
    protected $file;
    protected $container;
    protected $fullPath;
    protected $fileName;
    protected $resizes = [];
    protected $fileUploaded = null;
    protected $dates = true;
    protected $originalSave = true;
    protected $temp = false;

    public function __construct($file, $path = 'files/', $container = 'asset')
    {
        $this->file = $file;
        $this->container = $container;
        $this->path = $path;
    }

    public function addResize($prefix, $width = null, $height = null)
    {
        $this->resizes[$prefix] = [
            'width'     => $width,
            'height'    => $height
        ];
    }

    public function setTemp()
    {
        $this->temp = true;
    }

    public function uploadSave()
    {
        $fileName = Uuid::uuid4()->toString().'.'.$this->file->extension();
        $dates = $this->dates ? Date('Y-m-d').'/' : '';
        $this->path = 'storage/'.$this->path.$dates;
        $this->fileName = $this->path.$fileName;
        if($this->temp) {
            $this->fullPath = app_path('temp/'.$this->path);
        } else {
            $this->fullPath = public_path($this->path);
        }

        //Creando directorio

        //Resizes
        $img = Image::make($this->file->getRealPath());

        //Llenando datos de file uploaded
        $this->fileUploaded['filesize'] = $img->filesize();
        $this->fileUploaded['width'] = $img->width();
        $this->fileUploaded['height'] = $img->height();
        $this->fileUploaded['originalName'] = $this->file->getClientOriginalName();
        $this->fileUploaded['extension'] = $this->file->getClientOriginalExtension();
        $this->fileUploaded['mime'] = $this->file->getMimeType();
        $this->fileUploaded['cache'] = $dates.$fileName;
        $this->fileUploaded['url'] = asset($this->fileName);

        //Cargando resizes
        foreach ($this->resizes as $prefix => $res) {
            $img->resize($res['width'], $res['height'], function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($this->fullPath.$prefix.'_'.$fileName);
            $this->resizes[$prefix]['url'] = asset($this->path.$prefix.'_'.$fileName);
        }

        //Guardando original
        if($this->originalSave) {
            $this->file->move($this->fullPath, $fileName);
        } else {
            if(file_exists($this->file->getRealPath())) {
                unlink($this->file->getRealPath());
            }
        }
    }

    public function setDates($bool)
    {
        $this->dates = $bool;
    }

    public function getResizes()
    {
        return $this->resizes;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFileUploaded()
    {
        return $this->fileUploaded;
    }
}
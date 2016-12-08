<?php

namespace Alex\Model;

class Image extends Model
{
    protected $table = "images";

    public function addNewImageForHotel($idHotel, $path)
    {
        $this->app['db']->insert($this->table, array('id_hotel' => $idHotel, 'path' => $path));
        $id = $this->app['db']->lastInsertId();
        return $id;
    }

    public function getImagesForHotel($idHotel)
    {
        return $this->app['db']->fetchAll("SELECT * FROM {$this->table} WHERE id_hotel = {$idHotel}");
    }

}
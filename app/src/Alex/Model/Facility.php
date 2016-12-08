<?php

namespace Alex\Model;

class Facility extends Model
{
    protected $table = 'facilities';

    public function getIdFacilityByName($name)
    {

        $facility = $this->app['db']->fetchAssoc("SELECT * FROM {$this->table} WHERE name = '{$name}'");
        if (isset($facility['id'])) {
            return $facility['id'];
        }
        return '';
    }

    public function addNewFacility($name)
    {
        $this->app['db']->insert($this->table, array('name' => $name));
        $id = $this->app['db']->lastInsertId();
        return $id;
    }

    public function addFacilityForHotel($idHotel, $idFacility)
    {
        $this->app['db']->insert('hotel_facilities', array('id_hotel' => $idHotel, 'id_facility' => $idFacility));
        $id = $this->app['db']->lastInsertId();
        return $id;
    }

    public function getFacilitiesForHotel($idHotel)
    {
        return $this->app['db']->fetchAll("SELECT * FROM {$this->table} WHERE id IN (SELECT id_facility FROM hotel_facilities WHERE id_hotel = {$idHotel})");
    }

}
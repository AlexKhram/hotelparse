<?php

namespace Alex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class HotelController
{
    public function index(Request $request, Application $app)
    {
        $hotels = $app['modelHotel']->getHotels();

        return $app['twig']->render('index.twig', [
            'hotels' => isset($hotels[0]) ? $hotels[0] : false
        ]);
    }

    public function parseData(Request $request, Application $app)
    {
        $app['modelParser']->parseData("http://www.booking.com/hotel/ua/fairmont-grand-kiev.ru.html");

        return $app->redirect('/');
    }


}
<?php
/** Current weather and the flavor tip for it (external API: Open-Meteo) */
Flight::route('GET /weather', function () {
    Flight::json(Flight::weather_service()->get_current());
});
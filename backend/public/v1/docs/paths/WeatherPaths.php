<?php
use OpenApi\Attributes as OA;

class WeatherPaths
{
    #[OA\Get(
        path: '/weather',
        tags: ['weather'],
        summary: 'Current weather and a flavor tip (external API: Open-Meteo)',
        responses: [
            new OA\Response(response: 200, description: 'The current weather', content: new OA\JsonContent(ref: '#/components/schemas/Weather')),
            new OA\Response(response: 503, description: 'The weather API is not available', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWeather() {}
}
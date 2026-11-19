<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/exceptions.php';

/**
 * External API service: current weather from Open-Meteo.
 * The dashboard shows the temperature and a flavor tip for it.
 * If the API fails, an ExternalServiceException (503) is thrown and the rest of the application keeps working.
 */
class WeatherService
{
    const MESSAGES = [
        'cold' => 'Message for below 10°C (try out camomile, mint, or black tea flavors)',
        'mild' => 'Message for 10°C to 19°C (try out vanilla, strawberry, or cherry milkshake)',
        'warm' => 'Message for 20°C to 27°C (always a good time for ice cream)',
        'hot'  => 'Message for 28°C and above (ICE CREAM TIME!)',
    ];

    /** Short names for the WMO weather codes that Open-Meteo returns. */
    const DESCRIPTIONS = [
        0 => 'Clear sky', 1 => 'Mostly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
        45 => 'Fog', 48 => 'Fog',
        51 => 'Light drizzle', 53 => 'Drizzle', 55 => 'Heavy drizzle',
        61 => 'Light rain', 63 => 'Rain', 65 => 'Heavy rain',
        71 => 'Light snow', 73 => 'Snow', 75 => 'Heavy snow',
        80 => 'Rain showers', 81 => 'Rain showers', 82 => 'Heavy rain showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm with hail', 99 => 'Thunderstorm with hail',
    ];

    /** Current weather and the matching message. */
    public function get_current()
    {
        $data = $this->call_api();

        $temperature = $data['current']['temperature_2m'] ?? null;
        $code = $data['current']['weather_code'] ?? null;
        if (!is_numeric($temperature) || !is_numeric($code)) {
            $this->fail('The weather API answered with unexpected data.');
        }

        $category = $this->category($temperature);
        return [
            'city' => Config::WEATHER_CITY(),
            'temperature' => round((float) $temperature, 1),
            'weather_code' => (int) $code,
            'description' => self::DESCRIPTIONS[(int) $code] ?? 'Unknown',
            'category' => $category,
            'message' => self::MESSAGES[$category],
        ];
    }

    /** Which message group a temperature belongs to. */
    public function category($temperature)
    {
        if ($temperature < 10) {
            return 'cold';
        }
        if ($temperature < 20) {
            return 'mild';
        }
        if ($temperature < 28) {
            return 'warm';
        }
        return 'hot';
    }

    /** Send the request. Every kind of failure ends in fail(). */
    private function call_api()
    {
        $url = Config::WEATHER_API_URL() . '?' . http_build_query([
            'latitude' => Config::WEATHER_LATITUDE(),
            'longitude' => Config::WEATHER_LONGITUDE(),
            'current' => 'temperature_2m,weather_code',
            'timezone' => 'auto',
        ]);

        $context = stream_context_create(['http' => [
            'method' => 'GET',
            'timeout' => (float) Config::WEATHER_TIMEOUT(),   // do not wait for the API forever
            'ignore_errors' => true,                          // read the body even on 4xx/5xx
            'header' => "Accept: application/json\r\n",
        ]]);

        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            $this->fail('The weather API could not be reached (timeout or network error).');
        }

        $status = $this->status_code($http_response_header ?? []);
        if ($status !== 200) {
            $this->fail("The weather API answered with HTTP $status.");
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            $this->fail('The weather API answered with invalid JSON.');
        }
        return $data;
    }

    private function status_code($headers)
    {
        // The first header line looks like: HTTP/1.1 200 OK
        return isset($headers[0]) && preg_match('#\s(\d{3})\s#', $headers[0] . ' ', $m) ? (int) $m[1] : 0;
    }

    /** Log the technical reason, give the user a friendly message (HTTP 503). */
    private function fail($reason)
    {
        error_log('[WeatherService] ' . $reason);
        throw new ExternalServiceException('Weather information is not available right now. Please try again later.');
    }
}
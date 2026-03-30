<?php
$jar = tempnam('/tmp', 'cookie');
$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
$res = curl_exec($ch);
preg_match('/name="_token" value="(.*?)"/', $res, $matches);
$token = $matches[1] ?? '';

$ch2 = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query(['_token' => $token, 'email' => 'admin@test.com', 'password' => 'password']));
curl_setopt($ch2, CURLOPT_COOKIEJAR, $jar);
curl_setopt($ch2, CURLOPT_COOKIEFILE, $jar);
curl_exec($ch2);

// Find a checked in booking
$b_id = \App\Models\Booking::where('status', 'checked_in')->value('id');
if (!$b_id) { echo "No booking\n"; exit; }

$ch3 = curl_init("http://127.0.0.1:8000/bookings/{$b_id}/check-out");
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_POST, true);
curl_setopt($ch3, CURLOPT_POSTFIELDS, http_build_query(['_token' => $token]));
curl_setopt($ch3, CURLOPT_COOKIEJAR, $jar);
curl_setopt($ch3, CURLOPT_COOKIEFILE, $jar);
$out = curl_exec($ch3);
echo curl_getinfo($ch3, CURLINFO_HTTP_CODE) . "\n";
if (curl_getinfo($ch3, CURLINFO_HTTP_CODE) == 500) {
    file_put_contents('err.html', $out);
}

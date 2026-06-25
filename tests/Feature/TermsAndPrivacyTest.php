<?php

test('terms and conditions page returns successful response', function () {
    $response = $this->get(route('terms'));

    $response->assertStatus(200);
});

test('privacy policy page returns successful response', function () {
    $response = $this->get(route('privacy'));

    $response->assertStatus(200);
});

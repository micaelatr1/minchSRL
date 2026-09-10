<?php

use App\Helpers\NumberHelper;

test('zero', function () {
    expect(NumberHelper::toLiteral(0))->toBe('Cero 00/100');
});

test('one', function () {
    expect(NumberHelper::toLiteral(1))->toBe('Un 00/100');
});

test('numbers under 30', function () {
    expect(NumberHelper::toLiteral(5))->toBe('Cinco 00/100');
    expect(NumberHelper::toLiteral(10))->toBe('Diez 00/100');
    expect(NumberHelper::toLiteral(15))->toBe('Quince 00/100');
    expect(NumberHelper::toLiteral(20))->toBe('Veinte 00/100');
    expect(NumberHelper::toLiteral(21))->toBe('VeintiÚn 00/100');
    expect(NumberHelper::toLiteral(29))->toBe('Veintinueve 00/100');
});

test('tens', function () {
    expect(NumberHelper::toLiteral(30))->toBe('Treinta 00/100');
    expect(NumberHelper::toLiteral(45))->toBe('Cuarenta y cinco 00/100');
    expect(NumberHelper::toLiteral(99))->toBe('Noventa y nueve 00/100');
});

test('one hundred', function () {
    expect(NumberHelper::toLiteral(100))->toBe('Cien 00/100');
});

test('hundreds', function () {
    expect(NumberHelper::toLiteral(200))->toBe('Doscientos 00/100');
    expect(NumberHelper::toLiteral(150))->toBe('Ciento cincuenta 00/100');
    expect(NumberHelper::toLiteral(999))->toBe('Novecientos noventa y nueve 00/100');
});

test('thousands', function () {
    expect(NumberHelper::toLiteral(1000))->toBe('Mil 00/100');
    expect(NumberHelper::toLiteral(1500))->toBe('Mil quinientos 00/100');
    expect(NumberHelper::toLiteral(1999))->toBe('Mil novecientos noventa y nueve 00/100');
});

test('multiple thousands', function () {
    expect(NumberHelper::toLiteral(2000))->toBe('Dos mil 00/100');
    expect(NumberHelper::toLiteral(10000))->toBe('Diez mil 00/100');
    expect(NumberHelper::toLiteral(999999))->toBe('Novecientos noventa y nueve mil novecientos noventa y nueve 00/100');
});

test('millions', function () {
    expect(NumberHelper::toLiteral(1000000))->toBe('Un millÓn 00/100');
    expect(NumberHelper::toLiteral(1500000))->toBe('Un millÓn quinientos mil 00/100');
    expect(NumberHelper::toLiteral(2000000))->toBe('Dos millones 00/100');
});

test('with decimals', function () {
    expect(NumberHelper::toLiteral(1234.56))->toBe('Mil doscientos treinta y cuatro 56/100');
    expect(NumberHelper::toLiteral(0.99))->toBe('Cero 99/100');
    expect(NumberHelper::toLiteral(100.50))->toBe('Cien 50/100');
});

test('rounds decimals', function () {
    $result = NumberHelper::toLiteral(99.999);
    expect($result)->toContain('00/100');
});

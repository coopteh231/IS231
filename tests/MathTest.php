<?php 
use PHPUnit\Framework\TestCase;
use App\Math;

class MathTest extends TestCase {
    /** @test */
    public function it_throws_exception_when_dividing_by_zero() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Делить на ноль нельзя! Дважды НЕЗЯ-Я-Я!!");

        Math::divide(10, 0); // Должно выбросить исключение
    }
}

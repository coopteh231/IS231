<?php
use PHPUnit\Framework\TestCase;
use App\Services\ValidateOrderData;

class OrderDataTest extends TestCase 
{
    private array $data;
    private ValidateOrderData $obj;

    public function setUp():void {
        // Массив валидных данных для передачи в метод
        $this->data = [];
        $this->data['fio'] = "Иванов";
        $this->data['address'] = "Кемерово, ул.Тухачевского 32";
        $this->data['phone'] = "89007009911";
        $this->data['email'] = "ivanov@example.com";
        // Объект класса ValidateOrderData
        $this->obj = new ValidateOrderData();
    }

    public function testValidateOrderData(): void {
        $this->assertSame( true, 
                           $this->obj->validate($this->data) );
        $this->data['email'] = "ivanovexample.com";                           
        $this->assertSame( false, 
                           $this->obj->validate($this->data) );                           
        $this->data['email'] = "ivanov@example.com";
        $this->data['phone'] = "9007009911";    // неправильный номер (1 цифры не хватает)
        $this->assertSame( false, 
                           $this->obj->validate($this->data) );                           
    }
}

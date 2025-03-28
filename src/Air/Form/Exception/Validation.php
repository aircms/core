<?php

namespace Air\Form\Exception;

use Air\Form\Form;
use Exception;

class Validation extends Exception
{
  public Form $form;

  public function __construct(Form $form)
  {
    $this->form = $form;
    parent::__construct("Form validation exception", 400);
  }

  public function getForm(): Form
  {
    return $this->form;
  }
}

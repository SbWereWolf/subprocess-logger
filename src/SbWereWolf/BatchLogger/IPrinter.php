<?php

namespace SbWereWolf\BatchLogger;

interface IPrinter
{
    public function print(array $messages);
}
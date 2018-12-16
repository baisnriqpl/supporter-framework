<?php

  namespace Vendor\Storage;

  interface RateLimiter
  {
  	public function all();

  	public function put($key, $value);

  	public function get($key);

  	public function has($key);
  }

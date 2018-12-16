<?php
	
	namespace Vendor\Verification;
	use DB;
	use Vendor\Http\History;

	class Verification
	{

		protected $message = [];
		public $rules;
		protected $data;
		protected $initialRules;
		protected $lan = [];

		public function __construct($rules, $data, $lan = [])
		{
			$this->data = $data;
			$this->initialRules = $rules;
			$this->lan = $lan;

			$this->setRules($rules); 
		}

		public function setRules($rules)
		{
			$this->initialRules = $rules;

	        $this->rules = [];

	        $rules = $this->explodeRules($this->initialRules);

	        $this->rules = array_merge($this->rules, $rules);

	        return $this;
		}

		private function explodeRules($rules)
		{

	        foreach ($rules as $key => $rule)
	        {   
	            $rules[$key] = is_string($rule) ? explode('|', $rule) : $rule;   
	        }

	        return $rules;
		}

		private function passes()
		{
			$rules = $this->rules;

			foreach ($rules as $attribute => $rule) {				
				$this->validate($attribute, $rule);
			}

			return ! $this->message;
		}

		private function getRuleParameters($rules)
		{
			$key = $rules[0];
			$rule = $rules[1];

			if (is_numeric($key))
			{
				if (mb_strpos($rule, ':'))
				{
					$parameters = explode(':', $rule);
					$rule = $parameters[0];
					$parameters = $parameters[1];	
				}
			}
			else
			{
				$rule  = $key;
				$parameters = $rules[1];
			}

			if (! empty($parameters))
			{
				if (is_string($parameters))
				{
					if (strstr($parameters, '/'))
					{
						$parameters = [$parameters];
					}
					else
					{
						$parameters = explode(',', $parameters);
					}
				}
			}

			return [
				'verify'	=>	$rule,
				'method'	=>	"validate" . ucfirst($rule),
				'parameters'=>	! empty($parameters) ? $parameters : ''
			];
		}

		public function fails()
		{
			return ! $this->passes();
		}

		public function errors()
		{
			$history = new History();

			$history->error($this->first());
			return $this;
		}

		public function first()
		{
			foreach ($this->message as $key => $val) {
				return $val;
			}
			return null;
		}

		public function all()
		{
			return $this->message;
		}

		private function validate($attribute, $rules)
		{
			foreach ($rules as $key => $rule)
			{
				$parameters = $this->getRuleParameters([$key, $rule]);

				$method = $parameters['method'];

				$this->$method($attribute, $parameters);
			}
		}

		private function changeFunction($attribute, $function, $rule)
		{
			if (isset($attribute) && ! $function($attribute))
			{
				$this->message($attribute, $rule);
				return false;
			}

			return true;
		}

		private function validateMin($attribute, $rule)
		{

			if (array_key_exists($attribute, $this->data))
			{
				$len = (int)mb_strlen($this->data[$attribute]);

				$min = (int)$rule['parameters'][0];

				if ($len < $min)
				{
					$this->message($attribute, $rule);
					return false;
				}
			}

			return true;
		}

		private function validateRequired($attribute, $rule)
		{
			if (! array_key_exists($attribute, $this->data) || $this->data[$attribute] == null || $this->data[$attribute] === '') {

				$this->message($attribute, $rule);
				return false;
			}

			return true;
		}

		private function validateString($attribute, $rule)
		{
			if (isset($this->data[$attribute]) && ! $this->changeFunction($this->data[$attribute], 'is_string', $rule)) {
				return false;
			}

			return true;
		}

		private function validateNumber($attribute, $rule)
		{
			if (isset($this->data[$attribute]) && ! $this->changeFunction($this->data[$attribute], 'is_numeric', $rule)) {
				return false;
			}

			return true;
		}

		private function validateInt($attribute, $rule)
		{
			if (isset($this->data[$attribute]) && ! $this->changeFunction($this->data[$attribute], 'is_int', $rule)) {
				return false;
			}

			return true;
		}

		private function validateArray($attribute, $rule)
		{
			if (isset($this->data[$attribute]) && ! $this->changeFunction($this->data[$attribute], 'is_array', $rule)) {
				return false;
			}

			return true;
		}

		private function searchDB($attributes, $parameters)
		{
			if (isset($this->data[$attributes]))
			{
				return DB::table($parameters[0])->where(
					 ! empty($parameters[1]) ? $parameters[1] : $attributes, $this->data[$attributes]
				 )->exist();
			}

			return false;
		}

		private function validateUnique($attributes, $rule)
		{
			if (isset($this->data[$attributes]) && $this->searchDB($attributes, $rule['parameters'])) {
				$this->message($attributes, $rule);
				return false;
			}

			return true;
		}

		private function validateExist($attribute, $rule)
		{
			if (! $this->searchDB($attribute, $rule['parameters'])) 
			{
				$this->message($attribute, $rule);
				return false;
			}

			return true;
		}

		private function validateIn($attribute, $rule)
		{
			if (isset($this->data[$attribute]))
			{
				if (! in_array($this->data[$attribute], $rule['parameters']))
				{	
					$this->message($attribute, $rule);
					return false;
				}
			}

			return true;
		}

		private function validateSame($attribute, $rule)
		{
			$parameter = $rule['parameters'][0];

			if (isset($this->data[$attribute]) && isset($this->data[$parameter]))
			{
				if ($this->data[$attribute] !== $this->data[$parameter])
				{
					$this->message($attribute, $rule);
					return false;
				}
			}
			return true;
		}

		private function validateDifferent($attribute, $rule)
		{
			$parameter = $rule['parameters'][0];

			if (isset($this->data[$attribute]) && isset($this->data[$parameter]))
			{
				if ($this->data[$attribute] == $this->data[$parameter])
				{
					$this->message($attribute, $rule);
					return false;
				}
			}
			return true;
		}

		private function validateRegex($attribute, $rule)
		{
			if (isset($this->data[$attribute]))
			{
				if (! preg_match($rule['parameters'][0], $this->data[$attribute]))
				{
					unset($rule['parameters'][0]);
					$this->message($attribute, $rule);
					return false;
				}
			}

			return true;
		}

		private function message($attribute, $rule)
		{
			$this->message[] =  $this->lan($attribute, $rule); 
		}

		private function messageBag($rule)
		{
			$rule = explode(':', $rule);

			return [
				'required'		=>		'required',
				'unique'		=>		'unique'
				][$rule[0]];

		}

		private function lan($attribute, $rule)
		{
			$langBag = $this->lan;

			foreach ($langBag as $key => $value)
			{
				if (strstr($key, '.'))
				{
					$keys = explode('.', $key);

					if ($rule['verify'] == $keys[1] && $attribute == $keys[0])
					{
						return $value;
					}
				}

				if ($attribute == $key)
				{
					$attribute = $langBag[$attribute];	
				}	
			}

			$parameters = isset($rule['parameters'][0]) ? $rule['parameters'][0] : '';

			return 'The ' . $attribute . ' must be ' . $rule['verify'] . ' ' . $parameters;
		}
	}











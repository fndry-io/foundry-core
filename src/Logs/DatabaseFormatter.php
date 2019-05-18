<?php

namespace Foundry\Core\Logs;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Monolog\Formatter\NormalizerFormatter;

class DatabaseFormatter extends NormalizerFormatter {

	/**
	 * type
	 */
	const LOG = 'log';
	const STORE = 'store';
	const CHANGE = 'change';
	const DELETE = 'delete';

	/**
	 * result
	 */
	const SUCCESS = 'success';
	const NEUTRAL = 'neutral';
	const FAILURE = 'failure';

	/**
	 * {@inheritdoc}
	 */
	public function format(array $record)
	{
		$record = parent::format($record);
		return $this->getDocument($record);
	}

	/**
	 * Convert a log message into an Database Log entity
	 *
	 * @param array $record
	 * @return array
	 */
	protected function getDocument(array $record)
	{
		$fills = $record['extra'];
		$fills['level'] = Str::lower($record['level_name']);
		$fills['description'] = $record['message'];
//		$fills['token'] = str_random(30);
		$context = $record['context'];
		if (!empty($context)) {
			$fills = array_merge($record['context'], $fills);
		}
		$fills['type'] = Arr::has($context, 'type') ? $context['type'] : self::LOG;
		$fills['result'] = Arr::has($context, 'result')  ? $context['result'] : self::NEUTRAL;
		return $fills;
	}
}
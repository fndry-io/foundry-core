<?php

namespace Foundry\Core\Logs;

class ExtraProcessor {

	public function __invoke(array $record)
	{
		$record['extra'] = [];
		if (!app()->runningInConsole()) {
			$record['extra'] = [
				'path' => request()->fullUrl(),
				'origin' => request()->headers->get('origin'),
				'user_id' => (request()->hasSession() && auth()->user()) ? auth()->user()->id : null,
				'ip' => request()->server('REMOTE_ADDR'),
				'user_agent' => request()->server('HTTP_USER_AGENT'),
				'session' => request()->hasSession() ? request()->session()->getId(): null
			];
		}
		return $record;
	}
}
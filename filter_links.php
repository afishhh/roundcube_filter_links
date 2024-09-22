<?php
class filter_links extends rcube_plugin
{
    public $task = 'mail';
    private $filters;

    public function init()
    {
        $this->add_hook('message_part_after', [$this, 'filter_message']);

        $pathc = "-:@A-Za-z0-9._~!%$&'()*+,;=";

        $this->filters = array_map(fn($value) => [$this, $value], [
            "<https://click\.pstmrk\.it/[$pathc]*?/([$pathc]*?)/[$pathc/]*>" => 'filter_pstmrk_it',
        ]);
    }

    public function filter_pstmrk_it($match)
    {
        return 'https://' . urldecode($match[1]);
    }

    public function filter_message($args)
    {
        if($args['type'] != 'plain')
            return null;

        $args['body'] = preg_replace_callback_array(
            $this->filters,
            $args['body'],
        );

        return $args;
    }
}

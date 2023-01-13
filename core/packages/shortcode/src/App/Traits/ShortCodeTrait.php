<?php

namespace Bo\Shortcode\App\Traits;


use Bo\Shortcode\App\Models\Shortcode;

trait ShortCodeTrait
{
    public function getShortcode(string $field)
    {
        if ($this->getAttribute($field)) {
            preg_match_all("/\[short-code\]\S+\[\/short-code\]/", $this->getAttribute($field), $match_regex_shortcode);
            foreach ($match_regex_shortcode as $shortcode) {
                $shortcode_model = Shortcode::where('key', str_replace(["[short-code]", "[/short-code]"], "", $shortcode))->first();
                if ($shortcode_model) {
                    if ($shortcode_model->type == 'source') {
                        return str_replace($shortcode, $shortcode_model->value, $this->getAttribute($field));
                    } elseif ($shortcode_model->type == 'view') {
                        if (view()->exists($shortcode_model->value)) {
                            $option = get_repeater_data_key_value($shortcode_model->option, 'key_variable', 'value_variable');
                            return str_replace($shortcode, view($shortcode_model->value, $option)->render(), $this->getAttribute($field));
                        }
                    }
                }
            }
        }
        return null;
    }
}

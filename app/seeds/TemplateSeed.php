<?php

use Phinx\Seed\AbstractSeed;

class TemplateSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $templates = $this->getTemplates();
        $newTemplates = [];
        if (empty($templates["default"])) {
            $newTemplates["default"] = ["name" => "default"];
        }
        if (empty($templates["api"])) {
            $newTemplates["api"] = ["name" => "api"];
        }

        if (!empty($newTemplates)) {
            $this->insert("template", array_values($newTemplates));
            $templates = $this->getTemplates();

            $fieldData = [
                "default" => [
                    [
                        "name" => "Date/Time",
                        "field_name" => "timestamp",
                        "type" => "int",
                    ],
                    [
                        "name" => "Log Level",
                        "field_name" => "level",
                        "type" => "int",
                    ],
                    [
                        "name" => "System",
                        "field_name" => "system",
                        "type" => "string",
                    ],
                    [
                        "name" => "Server",
                        "field_name" => "server",
                        "type" => "string",
                    ],
                    [
                        "name" => "Message",
                        "field_name" => "message",
                        "type" => "text",
                    ],
                    [
                        "name" => "Execution ID",
                        "field_name" => "exec_id",
                        "type" => "string",
                    ],
                ]
            ];

            $apiFields = [
                [
                    "name" => "URL",
                    "field_name" => "url",
                    "type" => "string",
                ],
                [
                    "name" => "Method",
                    "field_name" => "method",
                    "type" => "string",
                ],
                [
                    "name" => "Request Headers",
                    "field_name" => "request_headers",
                    "type" => "text",
                ],
                [
                    "name" => "Request Body",
                    "field_name" => "request",
                    "type" => "text",
                ],
                [
                    "name" => "Response Headers",
                    "field_name" => "response_headers",
                    "type" => "text",
                ],
                [
                    "name" => "Response Body",
                    "field_name" => "response",
                    "type" => "text",
                ],
                [
                    "name" => "Duration",
                    "field_name" => "duration",
                    "type" => "float",
                ],
            ];
            $fieldData["api"] = array_merge($fieldData["default"], $apiFields);

            foreach ($newTemplates as $name => $data) {
                $fields = $fieldData[$name];
                $templateId = $templates[$name]["id"];
                foreach ($fields as $i => $field) {
                    $fields[$i]["template_id"] = $templateId;
                }
                $this->insert("field", $fields);
            }
        }

    }

    protected function getTemplates()
    {
        return [
            "default" => $this->fetchRow("SELECT * FROM template WHERE name = 'default'"),
            "api" => $this->fetchRow("SELECT * FROM template WHERE name = 'api'")
        ];
    }
}

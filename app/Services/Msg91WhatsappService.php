curl --location --request POST
  'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/'
  --header 'Content-Type: application/json'
  --header 'authkey: <authkey>'
  --data-raw '{
    "integrated_number": "919124420330",
    "content_type": "template",
    "payload": {
        "messaging_product": "whatsapp",
        "type": "template",
        "template": {
            "name": "customer",
            "language": {
                "code": "en",
                "policy": "deterministic"
            },
            "namespace": "73669fdc_d75e_4db4_a7b8_1cf1ed246b43",
            "to_and_components": [
                {
                    "to": [
                        "<list_of_phone_numbers>"
                    ],
                    "components": {
                        "header_1": {
                            "type": "text",
                            "value": "<{{1}}>"
                        },
                        "body_1": {
                            "type": "text",
                            "value": "value1"
                        }
                    }
                }
            ]
        }
    }
}'

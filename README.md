Lead List API Plugin -  

O plugin Lead List API é uma solução para WordPress que permite criar uma API RESTful para receber e gerenciar leads diretamente do seu site WordPress. Ele oferece uma maneira conveniente e segura de coletar informações de leads de diferentes fontes, como formulários de contato, páginas de destino e outros pontos de entrada.

Principais Funcionalidades

Receba Leads via API: tenha um endpoint para receber dados de leads por meio de solicitações HTTP POST.
Segurança de Acesso: Proteja seu endpoint com autenticação baseada em token para garantir que apenas solicitações autorizadas sejam processadas.
Armazenamento de Dados Seguro: Todos os dados de leads são armazenados de forma segura no banco de dados.
Personalização Flexível: Personalize facilmente os campos de dados do lead de acordo com suas necessidades específicas.
Exportação de dados: Exporte os dados de leads em formato CSV.

Instalação

Faça o download do arquivo zip do plugin ou clone o repositório para o diretório /wp-content/plugins/ do seu site WordPress.
Ative o plugin por meio do menu "Plugins" no painel de administração do WordPress.
Configure suas opções de API e personalize o endpoint conforme necessário.

Uso

Após a instalação e configuração do plugin, você poderá começar a receber leads enviando solicitações HTTP POST para o endpoint da API. Certifique-se de incluir um cabeçalho de autorização válido contendo o token de acesso fornecido nas configurações do plugin.

Exemplo de solicitação HTTP POST usando cURL:

bash
Copy code
curl -X POST \
  https://seusite.com.br/wp-json/lead-list-api/v1/integration
  -H 'Authorization: Bearer SEU_TOKEN_DE_AUTORIZACAO' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "exemplo_name",
    "email": "exemplo_email",
    "state": "exemplo_state",
    "city": "exemplo_city",
    "telephone": "exemplo_telephone",
    "page_conversion": "www.site.com"
    }'
Configurações

O plugin fornece uma página de configurações no painel de administração do WordPress, onde você pode personalizar o endpoint da API, gerenciar o token de autenticação e visualizar estatísticas de leads recebidos.

Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para relatar problemas, enviar solicitações de recursos ou contribuir com código para aprimorar este plugin.Consulte a documentação de contribuição do WordPress para obter detalhes sobre como contribuir.

Suporte

Se você encontrar problemas ou precisar de ajuda, consulte a documentação do plugin ou entre em contato com o suporte através do site do plugin.

Licença

Este plugin é licenciado sob a Licença Pública Geral GNU versão 2 ou qualquer versão posterior.

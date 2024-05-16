Lead List API Plugin

O plugin Lead List API é uma solução para WordPress que permite criar uma API RESTful para receber e gerenciar leads diretamente do seu site WordPress. Ele oferece uma maneira conveniente e segura de coletar, visualizar, gerenciar e exportar informações de leads de diferentes fontes, como formulários de contato, páginas de destino e outros pontos de entrada.

Principais Funcionalidades

    Receba Leads via API: Endpoint para receber dados de leads por meio de solicitações HTTP POST.
    Segurança de Acesso: Proteja seu endpoint com autenticação baseada em token para garantir que apenas solicitações autorizadas sejam processadas.
    Armazenamento de Dados Seguro: Todos os dados de leads são armazenados de forma segura no banco de dados.
    Personalização Flexível: Personalize facilmente os campos de dados do lead de acordo com suas necessidades específicas.
    Exportação de Dados de Leads para CSV: Exporta dados de leads e conversões para um arquivo CSV.
    Visualização e Gerenciamento de Leads: Exibe uma lista de leads com suas respectivas informações e conversões na área administrativa.
    Adição e Exclusão de Campos Dinâmicos: Permite adicionar e excluir campos na tabela de leads.
    Gerenciamento de Tokens de API: Cria e gerencia tokens para acesso à API.

Instalação

    Faça o download do arquivo zip do plugin ou clone o repositório para o diretório /wp-content/plugins/ do seu site WordPress.
    Ative o plugin por meio do menu "Plugins" no painel de administração do WordPress.
    Configure suas opções de API e personalize o endpoint conforme necessário.

Uso

Receber Leads via API

Após a instalação e configuração do plugin, você poderá começar a receber leads enviando solicitações HTTP POST para o endpoint da API. Certifique-se de incluir um cabeçalho de autorização válido contendo o token de acesso fornecido nas configurações do plugin.

Exemplo de solicitação HTTP POST usando cURL:

bash

curl -X POST \
  https://seusite.com.br/wp-json/lead-list-api/v1/integration \
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

Exportar Dados de Leads para CSV

Para exportar os dados dos leads, adicione o parâmetro export_lead_data=csv à URL do painel administrativo do WordPress:

https://seusite.com.br/wp-admin?export_lead_data=csv

Visualizar e Gerenciar Leads

    Vá até o menu "Lead List API" no painel administrativo.
    Clique em "Leads" para ver a lista de leads cadastrados e suas respectivas conversões.
    Use a paginação para navegar entre os leads e visualize as informações detalhadas de cada lead.

Adicionar e Excluir Campos de Leads

    Acesse a aba "Gerenciar Campos" no menu "Lead List API".
    Para adicionar um novo campo, insira o nome do campo no formulário e clique em "Adicionar Campo".
    Para excluir um campo, selecione o campo desejado e clique em "Excluir Campo".

Gerenciar Tokens de API

    Acesse a aba "Tokens" no menu "Lead List API".
    Para adicionar um novo token, insira o nome do token no formulário e clique em "Gerar Token".
    Para excluir um token, selecione o token desejado e clique em "Excluir Token".

Configurações

O plugin fornece uma página de configurações no painel de administração do WordPress, onde você pode personalizar o endpoint da API, gerenciar o token de autenticação e visualizar estatísticas de leads recebidos.

Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para relatar problemas, enviar solicitações de recursos ou contribuir com código para aprimorar este plugin. Consulte a documentação de contribuição do WordPress para obter detalhes sobre como contribuir.
Suporte

Se você encontrar problemas ou precisar de ajuda, consulte a documentação do plugin ou entre em contato com o suporte através do site do plugin.

Licença

Este plugin é licenciado sob a Licença Pública Geral GNU versão 2 ou qualquer versão posterior.
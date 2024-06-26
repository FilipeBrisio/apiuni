# apiunit
Para começar a executar a API, é necessário seguir uma série de passos que envolvem a instalação de ferramentas e a configuração do ambiente. Abaixo está um resumo dos passos necessários para configurar e executar a API:

Instalação do Symfony CLI: O Symfony CLI é uma ferramenta que facilita o desenvolvimento com o framework Symfony. Você pode instalá-lo executando o comando scoop install symfony-cli no terminal.

Obtenção do Repositório do GitHub: Clone ou baixe o repositório da API do GitHub para o seu ambiente de desenvolvimento.

Configuração do Docker: Utilize o Docker Compose para criar um contêiner para a sua aplicação. Execute o comando docker-compose up --build -d no terminal para iniciar o contêiner.

Instalação do Docker Desktop e Composer: Certifique-se de que o Docker Desktop e o Composer estejam instalados em seu sistema. Essas ferramentas são necessárias para gerenciar os contêineres Docker e as dependências do projeto, respectivamente.

Instalação de Dependências do Composer: No terminal, navegue até o diretório do projeto e execute o comando composer require para instalar todas as dependências do projeto.

Criação de Migrações de Dados: Antes de executar a migração de dados, é necessário limpar os arquivos da pasta de migração. Em seguida, utilize os comandos symfony console make:migration e symfony console doctrine:migrations:migrate para criar e executar as migrações de dados, respectivamente.

Inicialização do Servidor Symfony: Inicie o servidor Symfony executando o comando symfony serve no terminal. Isso iniciará o servidor local e permitirá que você acesse a API.

Teste da API: Após iniciar o servidor, você pode testar a API usando ferramentas como o Postman ou qualquer outro cliente de API.

Seguindo esses passos, você será capaz de configurar e executar a API Symfony em seu ambiente de desenvolvimento. Certifique-se de seguir cada passo cuidadosamente para evitar problemas durante o processo de configuração.
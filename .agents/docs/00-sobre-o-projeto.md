#Etapa 00
##Sobre o Projeto
O projeto Short trata-se de um encurtador de URL desenvolvido em Laravel 12 com banco de dados MySQL. O mesmo não precisa de cadastro para ser utilizado, bastando que quando o usuario acessar o projeto, haverá um input centralizado no centro da tela, sem bordas laterais (apenas em baixo, como uma linha), onde digitará a URL e clicará em encurtar, gerando um link curto típico de encurtadores do mercado, com poucos digitos. O usuário também poderá definir uma código próprio para um encurtador. Ex.: /meu-link-1-compartilhado. Cada link tera um pin, onde o usuário, ao digitar esse pin, terá acesso aos dados do link por ele criado em outra pagina (visto que não há login). O projeto deve poder rodar em subdominios em hospedagens compartilhadas. 

##Stack do Projeto
O projeto será desenvolvido usando Laravel 12, SQLite e Tailwind. O mesmo terá apenas um tema escuro por padrão, com destaques em laranja suave. 

##Arquitetura do banco de dados
Tabelas:
    - urls
        url_id -> unique
        original_link
        short_url -> unique
        pin_manager -> unique
    - visualizations
        visualization_id
        url_id
        acess_date_hour
        device_info
Quando uma url for inserida, deverá ser salva em original_link e gerar uma url encurtada (short_url). Pode haver mais de uma original_link, visto que vários usuários podem encurtar uma URL semelhante. Porém, isso deverá ser gerenciado pelo PIN, que será diferente para cada um. 
A tabela visualizations guarda informações básicas do link, como a id de urls.urld_id e a hora e data de acesso, além de informações básicas do dispositovo que acessou como Android/iOS/Windows e o navegador. 

##Boas práticas
O projeto deve ser responsivo tanto em computadores, quando dispositivos móveis. 
Após cada implementação, deverão ser criados testes unitários e verificações larastan, além de testes de navegação na interface.

##Para o README
Crie um readme que resuma os objetivos do projeto e detalhe a estrutura inicial do mesmo, para um primeiro commit do github. 
Ao final, o READ-ME será atualizado. 

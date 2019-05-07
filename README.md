# ssvo
Sistema de controle de solicitações de veículos oficiais

1. Criar a base de dados ssvo
2. Importar o arquivo ssvo.sql

3. Salvar o arquivo dts/iniSis.php.example como iniSis.php na mesma pasta,
e configurar a base de dados, o usuário do MySQL e a senha.
4. Salvar o arquivo sis_viaturas/codes/ajax/city.php.example como city.php na mesma pasta, 
e configurar a base de dados, o usuário do MySQL e a senha.

5. No arquivo iniSis.php tambm devem ser configuradas as definições de envio de e-mail.<br />
a) MAILADMIN é o email da pessoa que vai realizar as liberações das viaturas.<br />
b) MAILUSER é o email que enviará os avisos e informações.<br />
c) MAILPASS é a senha do email sistemas.<br />

O Sistema:<br />
Possui 2 módulos, o SIS_VIATURAS que é onde os usuários têm acesso para realizar as solicitações de viaturas e o ADMIN
que é onde os administradores de sistema tem acesso para cadastrar servidores, viaturas, liberar solicitações, 
cadastrar manutenções e etc.

Quando o usuário faz o login ele cai diretamente na página de solicitações (sis_viaturas) e caso ele tenha perfil de 
administrador aparecerá uma tarja azul no topo da tela que leva até o painel admin.

Usuário padrão:<br />
SIAPE: 1234567<br />
SENHA: 12345678<br />

Feito em PHP (5.6) com banco de dados em MySQL (5.7)

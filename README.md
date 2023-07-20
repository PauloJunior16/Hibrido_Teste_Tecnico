# Testes Adobe da Hibrido
Este repositório contém três módulos e seus respectivos arquivos desenvolvidos conforme desafio proposto.

## Teste 1: Meta Tag em Multi-site

### Funcionamento
O módulo desenvolvido foi o AddHreflang, ele possui um observer que faz o listening do evento cms_page_render que é acionado quando uma página CMS é renderizada.
Quando esta pagina é carregada o observer verifica se a página está atribuida a multiplas store views. Se estiver, o módulo adiciona a meta tag hreflang dinamicamente
a seção <head> da página, fornecendo as informações regionais.
O modulo tambem utiliza o sistema de blocos do Magento para inserir o um head adicional no layout da página, pra isso foi criado o AddHreflangBlock. Esse bloco lida coa geração
das meta tags com base na URL da página CMS e nos idiomas das exibições da loja.

### Instalação
1. Clone o repositorio para a maquina local ou baixe o arquivo zip.
2. Navegue até o diretorio onde o Magento está instalado.
3. Copie a pasta do modulo para o diretorio 'app/code'
4. Rode os seguintes comandos  do Magento no terminal:

   - bin/magento module:enable Hibrido_AddHreflang
   - bin/magento setup:upgrade
   - bin/magento cache:flush

## Teste 2: Trocando a cor dos botões
O módulo desenvolvido foi o ChangeButtonColor, a funcionalidade principal desse módulo é implementada na classe ChangeButtonColorCommand para realizar as operações via terminal(CLI).
Nessa classe o método getCssPath() é o responsável por determinar o caminho correto para os arquivos CSS da visualização da loja, levando em consideração o codigo da store view e o codigo
de localidade para constuir o caminho apropriado.
Foi criada também uma logica para seguir o padrão de cores em hexadecimal e tambem no digito verificado da store view, para asim evitar erros ao ser executado na linha de comando, tendo em 
vista que esses são os unicos parametros do comando e que são obrigatorios.

### Instalação
1. Clone o repositorio para a maquina local ou baixe o arquivo zip.
2. Navegue até o diretorio onde o Magento está instalado.
3. Copie a pasta do modulo para o diretorio 'app/code'
4. Rode os seguintes comandos  do Magento no terminal:

   - bin/magento module:enable Hibrido_ChangeButtonColor
   - bin/magento setup:upgrade
   - bin/magento cache:flush


## Teste 3: Nova Homepage
O módulo desenvolvido foi o NewHomePage, ele foi estruturado utilizando a classe UpgradeData  e criado o método createCmsBlocks, nele se encontra toda a logica para gerar
os blocos e widgets CMS via código e dentro de cada um possuir tags HTML, para isso foram feitas várias concatenações para abrir e fechar as tags corretamente para evitar erros
e serem repassadas corretamente dentro de cada bloco, sendo assim possivel visualizar na lista de blocos, no page builder, e quando adicionado a uma pagina, renderiza-lo no front.
Para a criação dos carroseis foram gerados dois arquivos no diretorio view/frontend , o primeiro foi o slider.js, onde dentro há uma função para renderizar os botões e criar a animação do slide no carossel
o outro arquivo foi o default.xml, adiciona o nosso slider.js ao <head> da página em que o carrosel se encontra.
Para que o funcionamento esteja correto todas as imagens foram adicionadas no caminho:
  - #### pub/media/wysiwyg/custom_homepage
Dentro estarão as seguintes pastas:
 - banners: para os banner principal rotativo,
 - banners_for_grid: para a grade de banners,
 - commercial_appeals: para a seção de apelos comerciais
 - 
Caso imagens sejam alteradas sera necessário modificação dos códigos desenvolvidos no modulo.

### Instalação
1. Clone o repositorio para a maquina local ou baixe o arquivo zip.
2. Navegue até o diretorio onde o Magento está instalado.
3. Copie a pasta do modulo para o diretorio 'app/code'
4. Rode os seguintes comandos  do Magento no terminal:

   - bin/magento module:enable Hibrido_NewHomePage
   - bin/magento setup:upgrade
   - bin/magento cache:flush



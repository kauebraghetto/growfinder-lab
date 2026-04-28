      <div class="hero">
        <div class="hero-tag">Módulo 02 · Dados</div>
        <h1>Da internet ao <em>conhecimento</em></h1>
        <p class="hero-sub">Antes de existir um modelo, existe uma quantidade absurda de texto. Entenda como ele virou inteligência.</p>
      </div>

      <div class="content">
        <div class="guia-section">
          <div class="guia-section-label">Coleta e filtragem</div>
          <h2>A matéria-prima: texto em escala industrial</h2>
          <p>O treinamento de um LLM começa com a coleta massiva de texto da internet. Pesquisadores baixam trilhões de palavras: Wikipedia, livros digitalizados, artigos científicos, código no GitHub, fóruns, notícias, documentação técnica.</p>
          <p>Esse material bruto passa por um processo intenso de filtragem: remoção de spam, conteúdo duplicado, idiomas indesejados e texto de baixa qualidade. O resultado é um corpus limpo que representa uma fração selecionada do conhecimento escrito da humanidade.</p>
          <p>O Common Crawl — uma das bases mais usadas — tem petabytes de dados. Depois de filtragem, restam centenas de gigabytes de texto de alta qualidade.</p>
        </div>

        <div class="analogy">
          <div class="analogy-label">Analogia</div>
          <p>É como construir uma biblioteca com o conteúdo de milhões de bibliotecas ao redor do mundo — mas antes de colocar cada livro na prateleira, uma equipe revisa se ele tem qualidade mínima para estar ali.</p>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Tokenização</div>
          <h2>Texto vira números: o processo de tokenização</h2>
          <p>Modelos de linguagem não leem palavras — leem tokens. Um token é um fragmento de texto, geralmente entre 3 e 6 caracteres. A palavra "tokenização" pode se tornar 3 tokens: "token", "iza", "ção".</p>
          <p>Todo o vocabulário do modelo — em torno de 50 a 100 mil tokens únicos — é mapeado para números inteiros. A partir daí, toda entrada e saída do modelo é, internamente, uma sequência de números.</p>
        </div>

        <div class="chips">
          <div class="chip"><strong>~100k</strong> tokens no vocabulário</div>
          <div class="chip"><strong>≈4 chars</strong> por token (inglês)</div>
          <div class="chip"><strong>≈750 palavras</strong> = 1.000 tokens</div>
          <div class="chip"><strong>Petabytes</strong> de dados brutos</div>
        </div>

        <div class="analogy">
          <div class="analogy-label">Por que tokens e não palavras?</div>
          <p>Palavras têm flexões, conjugações, prefixos e sufixos que explodem o vocabulário. Tokens permitem que o modelo represente qualquer palavra — mesmo inventada ou técnica — como combinação de fragmentos conhecidos. É mais eficiente e generaliza melhor.</p>
        </div>

        <div class="business">
          <div class="business-label">Para o seu negócio</div>
          <p>Quando você usa um LLM, está pagando por tokens. Entender isso explica por que <strong>prompts longos custam mais</strong>, por que respostas muito extensas aumentam o custo, e por que é possível otimizar o uso sem perder qualidade.</p>
          <p>Uma prática importante: quanto mais específico e limpo for o texto que você envia ao modelo, mais eficiente e precisa é a resposta. Qualidade da entrada determina qualidade da saída.</p>
        </div>
      </div>

      <div class="nav-footer">
        <div class="nav-footer-info">2 de 6 módulos</div>
        <div class="btn-group">
          <button class="btn btn-outline" onclick="goTo(0)">← Anterior</button>
          <button class="btn btn-primary" onclick="goTo(2)">Próximo →</button>
        </div>
      </div>

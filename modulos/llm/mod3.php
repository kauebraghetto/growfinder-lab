      <div class="hero">
        <div class="hero-tag">Módulo 03 · Arquitetura</div>
        <h1>O <em>motor</em> por dentro: Transformer e Atenção</h1>
        <p class="hero-sub">O paper de 2017 que mudou a IA: "Attention is All You Need". O que ele propôs e por que funcionou.</p>
      </div>

      <div class="content">
        <div class="guia-section">
          <div class="guia-section-label">A revolução de 2017</div>
          <h2>Por que o Transformer mudou tudo</h2>
          <p>Antes de 2017, redes neurais para linguagem processavam texto em sequência — palavra por palavra, da esquerda para a direita. Funcionava, mas perdia contexto em textos longos e era difícil de paralelizar.</p>
          <p>O paper "Attention is All You Need" da Google propôs uma arquitetura radicalmente diferente: o Transformer. Em vez de processar em sequência, ele permite que o modelo olhe para todos os tokens simultaneamente, calculando relações entre qualquer par de palavras no texto — independente da distância entre elas.</p>
        </div>

        <div class="analogy">
          <div class="analogy-label">O mecanismo de atenção em palavras simples</div>
          <p>Na frase "O banco estava na margem do rio", a palavra "banco" pode significar instituição financeira ou borda de um rio. O mecanismo de atenção permite que o modelo olhe para "rio" e "margem" ao mesmo tempo que processa "banco" — e decida qual significado faz mais sentido. Isso é Self-Attention.</p>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Parâmetros</div>
          <h2>O que o modelo realmente aprende</h2>
          <p>Parâmetros são os números internos do modelo — os pesos das redes neurais que compõem o Transformer. São eles que armazenam todo o "conhecimento" adquirido durante o treinamento.</p>
          <p>Um modelo de 7 bilhões de parâmetros tem 7 bilhões desses números, todos ajustados durante o treinamento para minimizar erros de previsão. Modelos maiores têm mais capacidade para capturar nuances — mas custam mais para rodar e treinar.</p>
        </div>

        <div class="chips">
          <div class="chip"><strong>GPT-2</strong> 1,5B params (2019)</div>
          <div class="chip"><strong>GPT-3</strong> 175B params (2020)</div>
          <div class="chip"><strong>Claude 3</strong> ~200B+ params</div>
          <div class="chip"><strong>Llama 3</strong> 8B / 70B / 405B</div>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Como o Transformer processa</div>
          <h2>O fluxo de uma resposta</h2>
          <p>Quando você digita uma pergunta, ela é tokenizada e transformada em vetores numéricos (embeddings). Esses vetores passam por camadas de atenção — dezenas ou centenas delas, dependendo do modelo — onde cada token acumula contexto de todos os outros. No final, o modelo gera o próximo token, e o processo se repete até a resposta estar completa.</p>
        </div>

        <div class="flow">
          <div class="flow-step">
            <div class="flow-step-num">01</div>
            <div class="flow-step-label">Texto entrada</div>
            <div class="flow-step-sub">Seu prompt</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">02</div>
            <div class="flow-step-label">Tokenização</div>
            <div class="flow-step-sub">Texto → números</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">03</div>
            <div class="flow-step-label">Embeddings</div>
            <div class="flow-step-sub">Números → vetores</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">04</div>
            <div class="flow-step-label">Atenção × N</div>
            <div class="flow-step-sub">N camadas</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">05</div>
            <div class="flow-step-label">Próximo token</div>
            <div class="flow-step-sub">Repetido até fim</div>
          </div>
        </div>

        <div class="business">
          <div class="business-label">Para o seu negócio</div>
          <p>Entender que o modelo processa contexto todo de uma vez explica por que <strong>dar mais contexto no prompt melhora a resposta</strong>. Também explica a janela de contexto — o limite de quanto texto o modelo pode "ver" em uma interação.</p>
          <p>Modelos maiores (mais parâmetros) não são sempre melhores para toda tarefa — um modelo menor e bem ajustado para o seu setor pode superar um gigante genérico. Isso tem impacto direto em custo e velocidade.</p>
        </div>
      </div>

      <div class="nav-footer">
        <div class="nav-footer-info">3 de 6 módulos</div>
        <div class="btn-group">
          <button class="btn btn-outline" onclick="goTo(1)">← Anterior</button>
          <button class="btn btn-primary" onclick="goTo(3)">Próximo →</button>
        </div>
      </div>

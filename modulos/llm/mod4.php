      <div class="hero">
        <div class="hero-tag">Módulo 04 · Treinamento</div>
        <h1>Como o modelo <em>aprende</em>: as três fases</h1>
        <p class="hero-sub">De texto bruto a assistente alinhado — o ciclo completo de criação de um LLM moderno.</p>
      </div>

      <div class="content">
        <div class="guia-section">
          <div class="guia-section-label">Fase 1 · Pretraining</div>
          <h2>Aprender a prever o próximo token</h2>
          <p>O pretraining é a fase mais cara e fundamental do treinamento. O modelo recebe sequências de texto e aprende uma única tarefa: prever qual é o próximo token. Parece trivial — mas para prever bem em qualquer contexto, o modelo precisa aprender gramática, fatos históricos, raciocínio lógico, código, medicina, direito.</p>
          <p>Esse processo roda em clusters de milhares de GPUs por semanas ou meses. Um único treinamento de modelo de ponta pode custar de dezenas a centenas de milhões de dólares e consumir o equivalente a megawatts de energia.</p>
          <p>O resultado é um modelo base — extraordinariamente capaz de completar texto, mas sem o comportamento de um assistente.</p>
        </div>

        <div class="analogy">
          <div class="analogy-label">Analogia</div>
          <p>É como aprender a língua portuguesa sendo forçado a completar bilhões de frases sem receber nenhuma instrução — apenas acerto e erro. Você aprende a sintaxe, o vocabulário e os padrões do mundo — mas não sabe ainda como ter uma conversa útil.</p>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Fase 2 · Supervised Fine-Tuning (SFT)</div>
          <h2>Transformando o modelo em assistente</h2>
          <p>Após o pretraining, o modelo base não sabe o que é uma instrução. Ele apenas completa texto. O SFT corrige isso: humanos especializados escrevem exemplos de conversas ideais — pergunta e resposta exemplar — e o modelo é treinado nesses pares.</p>
          <p>O dataset de SFT é muito menor (dezenas a centenas de milhares de exemplos), mas de qualidade altíssima. É essa fase que transforma o "completador de texto" em algo que parece um assistente.</p>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Fase 3 · Reinforcement Learning from Human Feedback (RLHF)</div>
          <h2>Refinando com feedback humano</h2>
          <p>Na fase final, humanos comparam pares de respostas do modelo e indicam qual é melhor. Esse sinal treina um modelo separado — chamado Reward Model — que aprende a pontuar respostas com base em preferência humana.</p>
          <p>O LLM é então ajustado para maximizar essa pontuação. O resultado é um modelo mais útil, mais seguro e mais alinhado com o que humanos consideram boa resposta. É a fase que refina o tom, a precisão e a recusa a conteúdo prejudicial.</p>
        </div>

        <div class="flow">
          <div class="flow-step">
            <div class="flow-step-num">1</div>
            <div class="flow-step-label">Pretraining</div>
            <div class="flow-step-sub">Trilhões de tokens</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">2</div>
            <div class="flow-step-label">Modelo Base</div>
            <div class="flow-step-sub">Completa texto</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">3</div>
            <div class="flow-step-label">SFT</div>
            <div class="flow-step-sub">Exemplos curados</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">4</div>
            <div class="flow-step-label">RLHF</div>
            <div class="flow-step-sub">Feedback humano</div>
          </div>
          <div class="flow-step">
            <div class="flow-step-num">5</div>
            <div class="flow-step-label">Assistente</div>
            <div class="flow-step-sub">Pronto para uso</div>
          </div>
        </div>

        <div class="business">
          <div class="business-label">Para o seu negócio</div>
          <p>Existe um quarto passo que não está no fluxo acima: <strong>fine-tuning específico para o seu domínio</strong>. Com dados da sua empresa, é possível ajustar um modelo para falar a linguagem do seu setor, usar os seus processos e responder como um especialista interno.</p>
          <p>Isso explica por que um LLM genérico dá respostas boas mas não perfeitas — e por que soluções personalizadas performam muito melhor em contextos industriais e técnicos.</p>
        </div>
      </div>

      <div class="nav-footer">
        <div class="nav-footer-info">4 de 6 módulos</div>
        <div class="btn-group">
          <button class="btn btn-outline" onclick="goTo(2)">← Anterior</button>
          <button class="btn btn-primary" onclick="goTo(4)">Próximo →</button>
        </div>
      </div>

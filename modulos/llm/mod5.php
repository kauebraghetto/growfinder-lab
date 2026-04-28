      <div class="hero">
        <div class="hero-tag">Módulo 05 · Comportamento</div>
        <h1>Por que o modelo se <em>comporta assim</em></h1>
        <p class="hero-sub">Alucinações, temperatura, limites de memória — entendendo o que acontece por baixo da superfície.</p>
      </div>

      <div class="content">
        <div class="guia-section">
          <div class="guia-section-label">Alucinação</div>
          <h2>Quando o modelo inventa — e por quê</h2>
          <p>Alucinação é quando um LLM gera informações falsas com aparente confiança. Cita autores que não existem, inventa datas, fabrica estatísticas. Não é mentira intencional — é uma consequência direta de como o modelo funciona.</p>
          <p>Lembre: o modelo foi treinado para prever o próximo token mais provável. Quando não sabe a resposta, ele ainda prevê — e o resultado pode ser plausível na forma, mas errado no conteúdo. O modelo não sabe o que não sabe.</p>
        </div>

        <div class="analogy">
          <div class="analogy-label">Analogia</div>
          <p>É como um funcionário que prefere dar uma resposta errada a admitir que não sabe. Ele aprendeu que respostas completas são recompensadas — então sempre completa, mesmo quando não deveria.</p>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Temperatura</div>
          <h2>O controle de criatividade vs. precisão</h2>
          <p>Temperatura é um parâmetro que controla o quanto o modelo "arrisca" nas suas previsões. Com temperatura baixa (próxima de zero), o modelo sempre escolhe o token mais provável — respostas previsíveis e precisas, ideais para extração de dados e análise. Com temperatura alta, o modelo explora opções menos prováveis — mais criatividade, mais variação, mais risco de erro.</p>
          <p>É um dos parâmetros mais importantes a calibrar dependendo da aplicação.</p>
        </div>

        <div class="chips">
          <div class="chip">Temperatura <strong>0.0–0.3</strong> → Extração, análise, código</div>
          <div class="chip">Temperatura <strong>0.7–1.0</strong> → Criação, brainstorm, marketing</div>
        </div>

        <div class="guia-section">
          <div class="guia-section-label">Janela de contexto</div>
          <h2>O limite de memória de trabalho</h2>
          <p>A janela de contexto é a quantidade máxima de tokens que o modelo pode processar em uma única interação — a soma do prompt de entrada e da resposta gerada. Quando esse limite é ultrapassado, o modelo "esquece" o que estava no início da conversa.</p>
          <p>Claude tem uma janela de 200 mil tokens — equivalente a um livro inteiro. GPT-4o tem 128 mil. Mas atenção: janelas maiores custam mais e são mais lentas. Há um custo computacional linear com o tamanho do contexto.</p>
        </div>

        <div class="divider"></div>

        <div class="guia-section">
          <div class="guia-section-label">Mitos vs. realidade</div>
          <h2>O que o modelo não é</h2>
        </div>

        <div class="myth-grid">
          <div class="myth-box false">
            <div class="myth-tag">❌ Mito</div>
            <p>O LLM tem memória entre conversas por padrão e lembra do que você disse semana passada.</p>
          </div>
          <div class="myth-box true">
            <div class="myth-tag">✓ Realidade</div>
            <p>Cada conversa começa do zero. Memória persistente é uma camada adicional — não é nativa do modelo.</p>
          </div>
          <div class="myth-box false">
            <div class="myth-tag">❌ Mito</div>
            <p>O LLM acessa a internet em tempo real e sabe o que aconteceu hoje.</p>
          </div>
          <div class="myth-box true">
            <div class="myth-tag">✓ Realidade</div>
            <p>O modelo tem uma data de corte de conhecimento. Acesso à internet é uma ferramenta separada, não padrão.</p>
          </div>
          <div class="myth-box false">
            <div class="myth-tag">❌ Mito</div>
            <p>O modelo "pensa" como um humano e tem consciência do que está fazendo.</p>
          </div>
          <div class="myth-box true">
            <div class="myth-tag">✓ Realidade</div>
            <p>O modelo opera por padrões estatísticos aprendidos. Não há compreensão, intenção ou consciência.</p>
          </div>
          <div class="myth-box false">
            <div class="myth-tag">❌ Mito</div>
            <p>Uma resposta confiante é sempre uma resposta correta.</p>
          </div>
          <div class="myth-box true">
            <div class="myth-tag">✓ Realidade</div>
            <p>O modelo pode errar com total confiança. Validação humana é sempre necessária em decisões críticas.</p>
          </div>
        </div>

        <div class="business">
          <div class="business-label">Para o seu negócio</div>
          <p>Os limites do modelo não são bugs — são características do sistema que precisam de design adequado. <strong>RAG</strong> (busca em base de conhecimento própria) resolve alucinação em domínios específicos. <strong>Memória externa</strong> resolve a falta de persistência. <strong>Agentes com acesso à internet</strong> resolvem o corte de conhecimento.</p>
          <p>Saber os limites é o que separa quem usa IA com resultado de quem se frustra com ela.</p>
        </div>
      </div>

      <div class="nav-footer">
        <div class="nav-footer-info">5 de 6 módulos</div>
        <div class="btn-group">
          <button class="btn btn-outline" onclick="goTo(3)">← Anterior</button>
          <button class="btn btn-primary" onclick="goTo(5)">Próximo →</button>
        </div>
      </div>

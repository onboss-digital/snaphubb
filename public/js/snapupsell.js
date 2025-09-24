// public/js/snapupsell.js
;(function (window, document) {
  class Snapupsell {
    static productId = null
    static customerId = null
    static upsell_productId = null
    static currency = 'BRL'
    static scriptTag = null
    static scriptOrigin = ''
    static endpoints = {
      accept: '/api/snapupsell/accept',
      reject: '/api/snapupsell/reject',
      price: '/api/snapupsell/price'
    }

    // 🔹 Estilos programáticos
    static styles = {
      select: {
        padding: '6px 10px',
        margin: '10px 5px',
        fontSize: '16px',
        border: '1px solid #ccc',
        borderRadius: '4px',
        backgroundColor: '#fff',
        width: '100%'
      },
      priceText: {
        fontSize: '24px',
        fontWeight: 'bold',
        color: '#333',
        marginLeft: '10px'
      },
      button: {
        padding: '8px 16px',
        fontSize: '16px',
        margin: '5px',
        borderRadius: '5px',
        border: 'none',
        cursor: 'pointer'
      },
      acceptButton: {
        backgroundColor: '#4CAF50',
        color: '#fff',
        width: '100%'
      },
      rejectButton: {
        backgroundColor: '#f44336',
        color: '#fff',
        width: '100%'
      },
      formupsell: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        maxWidth: '300px',
        lineHeight: '12px'
      }
    }

    static currencySymbols = {
      BRL: 'R$',
      USD: '$',
      EUR: '€'
    }

    static applyStyles(el, styleObj) {
      Object.assign(el.style, styleObj)
    }

    static init() {
      localStorage.setItem('isbuy', false)
      Snapupsell.preventClose(true)
      Snapupsell.scriptTag = document.getElementById('snapupsellScript')
      Snapupsell.scriptOrigin = new URL(Snapupsell.scriptTag.src).origin
      Snapupsell.endpoints = {
        accept: Snapupsell.scriptOrigin + '/api/snapupsell/accept',
        reject: Snapupsell.scriptOrigin + '/api/snapupsell/reject',
        price: Snapupsell.scriptOrigin + '/api/snapupsell/price'
      }
      const url = new URL(window.location.href)
      const customerId = url.searchParams.get('customerId')
      const upsell_productId = url.searchParams.get('upsell_productId')
      // 🔹 Se vier pela URL, salva no storage e seta na classe
      if (customerId && upsell_productId) {
        Snapupsell.customerId = customerId
        Snapupsell.upsell_productId = upsell_productId

        // salva no localStorage
        localStorage.setItem('snapupsell_customerId', customerId)
        localStorage.setItem('snapupsell_upsell_productId', upsell_productId)

        // remove os params da URL
        url.searchParams.delete('customerId')
        url.searchParams.delete('upsell_productId')
        window.history.replaceState({}, document.title, url.toString())
      } else {
        // 🔹 Se não veio por param, tenta recuperar do storage
        const storedCustomerId = localStorage.getItem('snapupsell_customerId')
        const storedUpsellProductId = localStorage.getItem('snapupsell_upsell_productId')

        if (storedCustomerId && storedUpsellProductId) {
          Snapupsell.customerId = storedCustomerId
          Snapupsell.upsell_productId = storedUpsellProductId
        }
      }
      //   if (customerId && upsell_productId) {
      //     Snapupsell.customerId = customerId
      //     Snapupsell.upsell_productId = upsell_productId
      //     url.searchParams.delete('customerId')
      //     url.searchParams.delete('upsell_productId')
      //     window.history.replaceState({}, document.title, url.toString())
      //   }

      // 🔹 Aplica estilo nos botões existentes
      const btnAccept = document.querySelector("button[onclick='Snapupsell.accept()']")
      const btnReject = document.querySelector("button[onclick='Snapupsell.reject()']")
      if (btnAccept)
        Snapupsell.applyStyles(btnAccept, {
          ...Snapupsell.styles.button,
          ...Snapupsell.styles.acceptButton
        })
      if (btnReject)
        Snapupsell.applyStyles(btnReject, {
          ...Snapupsell.styles.button,
          ...Snapupsell.styles.rejectButton
        })
    }

    static createButtons(container) {
      // Aceitar
      let btnAccept = document.createElement('button')
      btnAccept.textContent = 'Aceitar'
      btnAccept.addEventListener('click', () => Snapupsell.accept())
      Snapupsell.applyStyles(btnAccept, {
        ...Snapupsell.styles.button,
        ...Snapupsell.styles.acceptButton
      })
      container.appendChild(btnAccept)

      // Recusar
      let btnReject = document.createElement('button')
      btnReject.textContent = 'Recusar'
      btnReject.addEventListener('click', () => Snapupsell.reject())
      Snapupsell.applyStyles(btnReject, {
        ...Snapupsell.styles.button,
        ...Snapupsell.styles.rejectButton
      })
      container.appendChild(btnReject)
    }

    static async setProduct(productId, currency) {
      Snapupsell.productId = productId
      Snapupsell.currency = currency

      const prices = await this.getPrices(productId)

      if (prices.status === 'success') {
        // 🔹 Pega a div formupsell
        let container = document.getElementById('formupsell')
        if (!container) {
          container = document.createElement('div')
          container.id = 'formupsell'
          document.body.appendChild(container)
        }
        container.innerHTML = '' // limpa conteúdo existente
        Snapupsell.applyStyles(container, Snapupsell.styles.formupsell)

        // 🔹 Select de moedas
        let select = document.createElement('select')
        select.id = 'currencySelect'
        Snapupsell.applyStyles(select, Snapupsell.styles.select)

        prices.prices.forEach((p) => {
          let option = document.createElement('option')
          option.value = p.currency
          option.textContent = p.currency
          if (p.currency === currency) option.selected = true
          select.appendChild(option)
        })

        // 🔹 Tag de preço
        let title = document.createElement('h1')
        title.id = 'producttitle'
        Snapupsell.applyStyles(title, Snapupsell.styles.title)
        container.appendChild(title)
        title.textContent = prices.prices[0]['name']

        // 🔹 Tag de preço
        let description = document.createElement('h5')
        description.id = 'productdescription'
        Snapupsell.applyStyles(description, Snapupsell.styles.description)
        container.appendChild(description)
        description.textContent = prices.prices[0]['description']

        container.appendChild(select)

        // 🔹 Tag de preço
        let priceText = document.createElement('span')
        priceText.id = 'priceValue'
        Snapupsell.applyStyles(priceText, Snapupsell.styles.priceText)
        container.appendChild(priceText)

        // 🔹 Cria os botões
        Snapupsell.createButtons(container)

        // 🔹 Atualiza preço
        const updatePrice = (cur) => {
          const priceObj = prices.prices.find((p) => p.currency === cur)
          if (priceObj) {
            const symbol = Snapupsell.currencySymbols[cur.toUpperCase()] || cur
            priceText.textContent = `${symbol} ${(priceObj.unit_amount / 100).toFixed(2)}`
          }
        }

        updatePrice(currency)

        // 🔹 Mudar moeda
        select.addEventListener('change', (e) => {
          Snapupsell.currency = e.target.value
          updatePrice(Snapupsell.currency)
        })
      }
    }

    static async postData(endpoint, payload) {
      try {
        const res = await fetch(endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        })
        return await res.json()
      } catch (err) {
        console.error('Snapupsell erro:', err)
      }
    }

    static async getPrices(productId) {
      try {
        const url = `${Snapupsell.endpoints.price}?productId=${encodeURIComponent(productId)}`
        const res = await fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          }
        })
        return await res.json()
      } catch (err) {
        console.error('Snapupsell getPrices erro:', err)
      }
    }

    static async accept() {
      if (!Snapupsell.customerId || !Snapupsell.productId || !Snapupsell.upsell_productId) {
        console.error('Esta página não veio de um produto principal')
        return
      }
      
      if (localStorage.getItem('isbuy') === 'true') {
        Snapupsell.showModal('same')
        setTimeout(() => {
          Snapupsell.hideModal()
          Snapupsell.preventClose(false)
        }, 2000)
        return;
      }
      Snapupsell.showModal('processing')
      const response = await Snapupsell.postData(Snapupsell.endpoints.accept, {
        customerId: Snapupsell.customerId,
        productId: Snapupsell.productId,
        currency: Snapupsell.currency,
        upsell_productId: Snapupsell.upsell_productId,
        origin: window.location.href
      })

      if (response.status === 'success') {
        Snapupsell.showModal('success')
        Snapupsell.preventClose(false)
        localStorage.setItem('isbuy', true)
      } else if (response.status === 'payment_fail') {
        Snapupsell.showModal('fail')
        Snapupsell.preventClose(false)
      }

      if (response.url_redirect) {
        window.location.href = response.url_redirect + '?customerId=' + Snapupsell.customerId + '&upsell_productId=' + Snapupsell.upsell_productId
      } else {
        Snapupsell.showModal('same')
        setTimeout(() => {
          Snapupsell.hideModal()
          Snapupsell.preventClose(false)
        }, 2000)
      }
    }

    static async reject() {
      if (!Snapupsell.customerId || !Snapupsell.productId || !Snapupsell.upsell_productId) {
        console.error('Esta página não veio de um produto principal')
        return
      }

      const response = await Snapupsell.postData(Snapupsell.endpoints.reject, {
        customerId: Snapupsell.customerId,
        productId: Snapupsell.productId,
        currency: Snapupsell.currency,
        upsell_productId: Snapupsell.upsell_productId,
        origin: window.location.href
      })

      console.log(response, response.url_redirect)
      if (response.url_redirect) {
        window.location.href = response.url_redirect + '?customerId=' + Snapupsell.customerId + '&upsell_productId=' + Snapupsell.upsell_productId
      } else {
        Snapupsell.showModal('refuse')
        setTimeout(() => {
          Snapupsell.hideModal()
          Snapupsell.preventClose(false)
        }, 2000)
      }
    }

    // 🔹 Modal helper dentro de Snapupsell
    static createModal() {
      let modal = document.createElement('div')
      modal.id = 'snapupsellModal'
      Object.assign(modal.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0,0,0,0.5)',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: '9999',
        fontFamily: 'Arial, sans-serif',
        color: '#fff',
        flexDirection: 'column',
        textAlign: 'center',
        padding: '20px',
        boxSizing: 'border-box',
        display: 'none' // inicialmente oculto
      })

      // Título
      let title = document.createElement('h2')
      title.id = 'snapupsellModalTitle'
      modal.appendChild(title)

      // Mensagem
      let message = document.createElement('p')
      message.id = 'snapupsellModalMessage'
      modal.appendChild(message)

      document.body.appendChild(modal)

      return modal
    }

    // 🔹 Exibir modal
    static showModal(type = 'processing', text = '') {
      if (!Snapupsell.modal) Snapupsell.modal = Snapupsell.createModal()
      let title = document.getElementById('snapupsellModalTitle')
      let message = document.getElementById('snapupsellModalMessage')

      switch (type) {
        case 'processing':
          title.textContent = 'Processando pagamento'
          message.textContent = text || 'Por favor, não feche ou atualize a página...'
          break
        case 'success':
          title.textContent = 'Pagamento concluído'
          message.textContent = text || 'Obrigado pelo seu pagamento!'
          break
        case 'fail':
          title.textContent = 'Pagamento falhou'
          message.textContent = text || 'Ocorreu um erro, tente novamente.'
          break
        case 'same':
          title.textContent = 'Pagamento já efetuado'
          message.textContent = text || 'Você já adquiriu essa oferta.'
          break
        case 'refuse':
          title.textContent = 'Ok, não temos mais ofertas'
          message.textContent = text || 'Você recusou essa oferta.'
          break
      }

      Snapupsell.modal.style.display = 'flex'
    }

    // 🔹 Fechar modal
    static hideModal() {
      if (Snapupsell.modal) Snapupsell.modal.style.display = 'none'
    }

    // 🔹 Prevenir fechamento ou atualização
    static preventClose(state = true) {
      if (state) {
        window.addEventListener('beforeunload', Snapupsell.beforeUnloadHandler)
      } else {
        window.removeEventListener('beforeunload', Snapupsell.beforeUnloadHandler)
      }
    }

    static beforeUnloadHandler(e) {
      e.preventDefault()
      e.returnValue = ''
    }
  }

  Snapupsell.init()
  window.Snapupsell = Snapupsell
})(window, document)

const LAZY_EMBEDS_ACTIVATED = 'wp-block-lazy-embeds--activated'

/**
 * Add a prefetching hint.
 * @param {String} kind
 * @param {String} url
 * @param {String} [as]
 */
export const addPrefetch = (kind, url, as) => {
  const link = document.createElement('link')
  link.rel = kind
  link.href = url

  if (as) {
    link.as = as
  }

  link.crossorigin = true
  document.head.append(link)
}

/**
 * Preconnect to an array of URLs.
 * {@link https://developer.mozilla.org/en-US/docs/Web/HTTP/Link_prefetching_FAQ MDN Prefetching FAQ}
 * @param {Array} urls
 */
export const addPreconnectURLs = urls => {
  for (const url of urls) {
    addPrefetch('preconnect', url)
  }
}

/**
 * Replace the Lazy Embeds wrapper with the actual embed.
 * @param {HTMLElement} wrapper The Lazy Embeds wrapper to be replaced.
 * @param {String} iframeHTML The HTML code for the iframe.
 */
export const replaceEmbed = (wrapper, iframeHTML) => {
  wrapper.parentElement.classList.add(LAZY_EMBEDS_ACTIVATED)
  wrapper.insertAdjacentHTML('beforeend', iframeHTML)
}

/**
 * Initialize the embeds.
 * @param {Object} param
 * @param {String} param.selector Data attribute of the Lazy Embed.
 * @param {Function} param.iframeHTML Get the HTML of the actual iframe.
 * @param {Array} [param.preconnectURLs] List of URLs to preconnect on hover.
 */
export const initEmbeds = ({ selector, iframeHTML, preconnectURLs }) => {
  const items = document.querySelectorAll(`[${selector}]`)
  let preconnected = false

  for (const item of [...items]) {
    item.addEventListener('click', event => {
      // Make sure links in the Lazy Embed keep working
      if (event.target.closest('[href]')) {
        return
      }

      replaceEmbed(event.target, iframeHTML(event.target, selector))
    })

    if (preconnectURLs) {
      item.addEventListener(
        'pointerover',
        () => {
          if (!preconnected) {
            addPreconnectURLs(preconnectURLs)

            preconnected = true
          }
        },
        { once: true }
      )
    }
  }
}

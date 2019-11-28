import { initEmbeds } from './helpers'

window.addEventListener('DOMContentLoaded', () => {
  const embeds = [
    // YouTube
    {
      selector: 'data-lazy-embeds-youtube-id',
      preconnectURLs: ['https://www.youtube.com', 'https://www.google.com'],
      iframeHTML: (wrapper, selector) => {
        const id = encodeURIComponent(wrapper.getAttribute(selector))

        return `<iframe frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen src="https://www.youtube.com/embed/${id}?autoplay=1"></iframe>`
      }
    },
    // Vimeo
    {
      selector: 'data-lazy-embeds-vimeo-id',
      preconnectURLs: ['https://www.vimeo.com', 'https://www.vimeocdn.com'],
      iframeHTML: (wrapper, selector) => {
        const target = wrapper.closest(`[${selector}]`)
        const id = encodeURIComponent(target.getAttribute(selector))

        return `<iframe src="https://player.vimeo.com/video/${id}?dnt=1&autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`
      }
    }
  ]

  for (const embed of embeds) {
    initEmbeds(embed)
  }
})

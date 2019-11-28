let preconnected = false;

const addPrefetch = (kind, url, as) => {
  const linkElem = document.createElement("link");
  linkElem.rel = kind;
  linkElem.href = url;

  if (as) {
    linkElem.as = as;
  }

  linkElem.crossorigin = true;
  document.head.append(linkElem);
};

const warmConnections = () => {
  if (preconnected) return;

  // The iframe document and most of its subresources come right off youtube.com
  addPrefetch("preconnect", "https://www.youtube.com");
  // The botguard script is fetched off from google.com
  addPrefetch("preconnect", "https://www.google.com");

  preconnected = true;
};

const replaceYouTubeEmbed = wrapper => {
  const id = encodeURIComponent(
    wrapper.getAttribute("data-lazy-embeds-youtube-id")
  );

  const iframeHTML = `<iframe frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen src="https://www.youtube.com/embed/${id}?autoplay=1"></iframe>`;

  wrapper.parentElement.classList.add("wp-block-lazy-embeds--activated");
  wrapper.insertAdjacentHTML("beforeend", iframeHTML);
};

const replaceVimeoEmbed = wrapper => {
  const target = wrapper.closest("[data-lazy-embeds-vimeo-id]");
  const id = encodeURIComponent(
    target.getAttribute("data-lazy-embeds-vimeo-id")
  );

  const iframeHTML = `<iframe src="https://player.vimeo.com/video/${id}?dnt=1&autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;

  target.parentElement.classList.add("wp-block-lazy-embeds--activated");
  target.insertAdjacentHTML("beforeend", iframeHTML);
};

window.addEventListener("DOMContentLoaded", () => {
  const youTubeItems = document.querySelectorAll(
    "[data-lazy-embeds-youtube-id]"
  );
  const vimeoItems = document.querySelectorAll("[data-lazy-embeds-vimeo-id]");

  for (const item of [...youTubeItems]) {
    item.addEventListener("click", event => {
      replaceYouTubeEmbed(event.target);
    });

    item.addEventListener(
      "pointerover",
      () => {
        warmConnections();
      },
      { once: true }
    );
  }

  for (const item of [...vimeoItems]) {
    item.addEventListener("click", event => {
      if (event.target.closest("[href]")) {
        return;
      }

      replaceVimeoEmbed(event.target);
    });
  }
});

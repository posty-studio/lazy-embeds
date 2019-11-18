window.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll("[data-lazy-embeds-youtube-id]");

  for (const item of [...items]) {
    item.addEventListener("click", event => {
      const id = encodeURIComponent(
        event.target.getAttribute("data-lazy-embeds-youtube-id")
      );

      const iframeHTML = `<iframe frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen src="https://www.youtube.com/embed/${id}?autoplay=1"></iframe>`;

      event.target.classList.add("lazy-embeds-wrapper--activated");
      event.target.insertAdjacentHTML("beforeend", iframeHTML);
    });
  }
});

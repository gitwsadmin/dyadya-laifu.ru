const ajaxRequest = function (method, url, obConfig) {
  const objUrl = parseUrlQuery();
  const obData = {
    ajax_get: "Y",
    // ajax: "y",
    AJAX_REQUEST: "Y",
    AJAX_POST: "Y",
    bitrix_include_areas: "N",
  };
  const obDataExt = obConfig["data"] || {};
  const methods = method || "GET";
  let add_url = "";

  // add clear_cache
  if (typeof objUrl === "object" && objUrl && "clear_cache" in objUrl && objUrl.clear_cache == "Y") {
    add_url = "?clear_cache=Y";
  }

  //merge data
  const obTransferData = Object.assign({}, obData, obDataExt);

  //set data to FormData
  const formData = new FormData();
  const arData = Object.keys(obTransferData);
  for (let i = 0; i < arData.length; i++) {
    formData.append(arData[i], obTransferData[arData[i]]);
  }

  //new xhr request
  const xhr = new XMLHttpRequest();

  xhr.open(methods, url + add_url);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

  xhr.send(formData);

  //set event
  xhr.onload = function () {
    let responseObj = xhr.response;
    obConfig["context"].classList.remove("loading-state");
    if (xhr.status != 200) {
      obConfig["context"].innerHTML = "Error " + xhr.status + ": " + xhr.statusText;
    } else {
      const obData = BX.processHTML(responseObj);
      obConfig["context"].innerHTML = obData.HTML;
      setTimeout(function () {
        BX.ajax.processScripts(obData.SCRIPT);
      }, 0);
    }
  };

  //set event
  xhr.onerror = function () {
    obConfig["context"].classList.remove("loading-state");
    obConfig["context"].innerHTML = "Error";
  };
};

const readyHandler = function () {
  const tabs = document.querySelector(".element-list .tab-nav");
  if (tabs) {
    const tabsItem = tabs.querySelectorAll(".element-list .tab-nav__item");
    const jsParams = document.getElementById("js-request-data");

    tabs.addEventListener("click", function (e) {
      if (tabsItem.length) {
        const item = e.target;
        // check target element - is tab-nav__item
        if (item.classList.contains("tab-nav__item") && !item.classList.contains("active")) {
          const tabCode = item.dataset["code"];
          const tabsContent = document.querySelectorAll(".element-list .tab-content-block");
          const curTab = document.querySelector(".element-list .tab-content-block[data-code='" + tabCode + "']");

          // remove all class nav link
          for (let i = 0; i < tabsItem.length; i++) {
            tabsItem[i].classList.remove("active");
          }
          //set active nav link
          item.classList.toggle("active");

          // remove all class nav content
          for (let i = 0; i < tabsContent.length; i++) {
            tabsContent[i].classList.remove("active");
          }
          //set active nav content
          curTab.classList.toggle("active");

          // ajax request
          if (!item.classList.contains("clicked")) {
            const obData = {
              FILTER_HIT_PROP: tabCode,
              AJAX_PARAMS: jsParams.dataset["value"],
              GLOBAL_FILTER: curTab.dataset["filter"],
              TYPE_TEMPLATE: tabs.dataset["template"],
            };

            ajaxRequest("POST", arAllcorp3Options["SITE_DIR"] + "include/mainpage/comp_catalog_ajax.php", {
              data: obData,
              context: curTab,
            });

            //set clicked to prevent same ajax
            item.classList.add("clicked");
          }
        }
      }
    });
  }
};
document.addEventListener("DOMContentLoaded", readyHandler);

/* tabs scroll */
window.tabsInitOnReady = function () {
  $(".element-list .tab-nav-wrapper").scrollTab({
    tabs_wrapper: ".tab-nav",
    tab_item: "> .tab-nav__item",
    width_grow: 3,
    outer_wrapper: ".element-list .index-block__title-wrapper",
    onResize: function (options) {
      const top_wrapper = options.outer_wrapper_node;
      let all_width = top_wrapper[0].getBoundingClientRect().width;
      if (top_wrapper.length) {
        const tabs_wrapper = options.scrollTab;

        if (window.matchMedia("(max-width: 767px)").matches) {
          tabs_wrapper.css({
            // 'width': '100%',
            "max-width": all_width,
          });
          return true;
        }

        const title = top_wrapper.find(".index-block__part--left");
        const right_link = top_wrapper.find(".index-block__part--right");

        if (title.length) {
          all_width -= title.outerWidth(true);
        }

        if (right_link.length) {
          all_width -= right_link.outerWidth(true);
        }

        all_width -= Number.parseInt(tabs_wrapper.css("margin-left"));
        all_width -= Number.parseInt(tabs_wrapper.css("margin-right"));

        tabs_wrapper.css({
          "max-width": all_width,
          width: "",
        });
      }
      options.width = all_width;
    },
  });
};

(function ($) {
    "use strict"; // Start of use strict

    var searchString = null
    var container = null
    var tag_links = null
    var clear_search = null
    var pause_interval = 1000
    var last_call = 0

    window.onload = function () {
        searchString = document.getElementById('tools_search');
        clear_search = document.getElementById('clear_search');
        container = document.getElementById('content');
        tag_links = document.getElementsByClassName('tag_filter')

        for (let index = 0; index < tag_links.length; index++) {
            const element = tag_links[index];
            element.addEventListener('click', function() {
                searchString.value = this.getAttribute('data-tag')
                searchTools();
            })
        }
        clear_search.addEventListener('click', function() {
            searchString.value = ""
            searchTools();
        })
        searchString.addEventListener('keyup', searchTools)
        searchTools();
    }



    async function searchTools(){
        if(new Date().getTime() - last_call < pause_interval && searchString.value !== "") return
        var response = await fetch("/api/tools/" + searchString.value);
        var tools = await response.json();
        container.innerHTML = ""
        tools.forEach(element => {
            var element = createCard(element)
            container.appendChild(element)
        });
        last_call = new Date().getTime()
    }

    function createCard(tool){
        var element = document.createElement('div');
        element.classList.add('col-lg-4');
        element.classList.add('col-md-6');
        element.classList.add('mb-4');
        var card = document.createElement('div')
        card.classList.add('card')
        card.classList.add('h-100')
        element.appendChild(card)
        card.appendChild(createCardHeader(tool))
        card.appendChild(createCardBody(tool))
        card.appendChild(createCardFooter(tool))
        return element
    }
    function createCardHeader(tool){
        var img = document.createElement('img')
        img.classList.add('card-img-top')
            img.classList.add('tool-def-img')
        if(tool.photo == undefined || tool.photo.length == 0){
            img.src = '/img/default-tool-pic-listing.png'
            img.alt = "tool_pic"
        }else{
            img.src = "/upload/photos/" + tool.photo
            img.alt = tool.photo
        }
        return img
    }
    function createCardBody(tool){
        var body = document.createElement('div')
        body.classList.add('card-body')
        var title = document.createElement('h4')
        title.classList.add('card-title')
        body.appendChild(title)
        var link = document.createElement('a')
        link.href = "/tool/" + tool.code
        link.innerHTML = tool.name + ' ' + tool.model
        title.appendChild(link)

        var status = document.createElement('h5')
        body.appendChild(status)
        status.classList.add('text-muted')
        status.innerHTML = tool.code

        var icon = document.createElement('i')
        icon.classList.add(tool.statusIcon)

        status.appendChild(icon)

        var description = document.createElement('p')
        description.classList.add('card-text')
        body.appendChild(description)
        description.innerHTML = tool.description
        return body
    }
    function createCardFooter(tool){
        var footer = document.createElement('div')
        footer.classList.add('card-footer')
        if(tool.tags == null || tool.tags.length == 0){
            var noTags = document.createElement('small')
            noTags.classList.add('text-muted')
            noTags.innerHTML = "No tags for tool"
            footer.appendChild(noTags)
        }else{
            tool.tags.forEach(tag => {
                var t = document.createElement('small')
                t.classList.add('text-muted')
                t.innerHTML = '#' + tag
                footer.appendChild(t)
                footer.innerHTML += ' '
            })
        }
        return footer
    }
}()) 
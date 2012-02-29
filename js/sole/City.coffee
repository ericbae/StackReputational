$ ->
  
  class HeaderSearch extends Backbone.View
    el:"#header"
    
    events : 
      "keypress input#display_name" : "search_user"
      
    search_user : (e) =>
      if e.keyCode is 13
        sole.display_name = $(@el).find("input#display_name").val()
        sole.navigate "user/" + sole.display_name, true
  
  # main search option view
  class Search extends Backbone.View
    el:"#search_box"
    
    set_age : =>
      if parseInt(sole.age_from) > 0
        $(@el).find("input#age_from").val(sole.age_from)
      else
        $(@el).find("input#age_from").val("")
        
      if parseInt(sole.age_to) > 0
        $(@el).find("input#age_to").val(sole.age_to)
      else
        $(@el).find("input#age_to").val("")
      
      # underline the age all if not
      if sole.age_from is '' and sole.age_to is ''
        $(@el).find("#group0").addClass("selected")
        
    set_repute : =>
      $(@el).find(".repute").removeClass("selected")
      $("#" + sole.order_by).addClass("selected")
    
    set_tag : =>
      $(@el).find(".tag").removeClass("selected")
      $("#" + sole.tag).addClass("selected")
                
    events :
      "click .age_search"       : "search_age"
      "click .age_group"        : "search_age"
      "click .reset"            : "search_age"
      "click .repute"           : "search_repute"
      "click .tag"              : "search_tag"
      
    search_age : (e) =>
      $(".age_group").removeClass("selected")
      if e.currentTarget.className is "age_group"
        sole.age_from = $("#" + e.currentTarget.id).data("from")
        sole.age_to = $("#" + e.currentTarget.id).data("to")
      else
        sole.age_from = $(@el).find("input#age_from").val()
        sole.age_to = $(@el).find("input#age_to").val()
      $("#" + e.currentTarget.id).addClass("selected")
      @perform_search()
      
    search_repute : (e) =>
      sole.order_by = e.currentTarget.id
      @perform_search()
      
    search_tag: (e) =>
      sole.tag = e.currentTarget.id
      @perform_search()

    perform_search : =>
      sole.navigate "search/" + sole.age_from + "/" + sole.age_to + "/" + sole.tag + "/" + sole.order_by + "/0", true

  # main user model
  class UserModel extends Backbone.Model
  
  # main user collection
  class UserCollection extends Backbone.Collection
    model: UserModel
    url  : base + "sole_api/cities"
    fetch_users: (data, callback) => 
      @fetch
        data: data
        success: callback
  
  # main user view
  class UserItem extends Backbone.View
    className : "sixcol",
    template    :Handlebars.compile $("#user_hb").html()
    
    render: =>
      $(@el).append @template @model.toJSON()
      @ 
  
  # main user list 
  class UserList extends Backbone.View
    el      :"#user_box"
    
    initialize: =>
    
    display: =>
      $("#param_box").hide();
      $("#main_loading").show()
      if !@collection
        @collection = new UserCollection
        @offset = 0
      @collection.fetch_users({
        age_from    :sole.age_from
        age_to      :sole.age_to
        display_name:sole.display_name
        tag         :sole.tag
        order_by    :sole.order_by
        offset      :sole.offset
      }, @render)
    
    render: =>
      $("#param_box").show();
      $("#main_loading").hide()
      @set_pagination()
      @set_param_text()
      @offset += @limit
      user_arr  = [] 
      counter = 0
      $(@el).empty()
      
      @collection.each (user_model) =>
        user_item = new UserItem {model: user_model}
        user_arr.push user_item.render().el
        counter++
        if counter % 2 is 0
          $(user_item.el).addClass "last"
          $(@el).append($("<div class='row'>").append(user_arr))
          user_arr = []
        
      # append the users, if there are any remaining
      if user_arr.length > 0
        $(@el).append user_arr
        
      # move to the top of the page
      $("html, body").animate { scrollTop: 0 }, 0
    
    set_param_text : =>
      if (parseInt(sole.age_from) is 0 and parseInt(sole.age_to) is 1000) or (!sole.age_from and !sole.age_to)
        $("#param_box").find(".age").html("Age : all")
      else if parseInt(sole.age_from) is -1 and parseInt(sole.age_to) is -1
        $("#param_box").find(".age").html("Age : unknown")
      else
        $("#param_box").find(".age").html("Age : between " + sole.age_from + " to " + sole.age_to)
        
      order_by = "reputation"
      if sole.order_by is "reputation_change_day"
        order_by = "reputation gained today"
      else if sole.order_by is "reputation_change_week"
        order_by = "reputation gained in week"
      else if sole.order_by is "reputation_change_month"
        order_by = "reputation gained in month"
      else if sole.order_by is "reputation_change_quarter"
        order_by = "reputation gained in 3 month"
      else if sole.order_by is "reputation_change_year"
        order_by = "reputation gained in year"
      $("#param_box").find(".order_by").html("Order by " + order_by)
      
      if sole.tag.length > 0
        $("#param_box").find(".tag").html("Topic : " + sole.tag);
      else
        $("#param_box").find(".tag").html("Topic : all");        
    
    set_pagination : =>
      $.ajax
        url : base + "main/get_num_cities/" + sole.offset
        dataType:"JSON"
        data : 
          age_from    :sole.age_from
          age_to      :sole.age_to
          display_name:sole.display_name
          tag         :sole.tag
          order_by    :sole.order_by
          offset      :sole.offset
        success: (data) =>
          $("#param_box").find(".size").html(addCommas(data.total) + " cities found.")
          $("#pagination").empty().append data.pagination

  # pagination
  class Pagination extends Backbone.View
    el:"#pagination"
    
    events:
      "click a" : "paginate"
    
    paginate : (e) =>
      e.preventDefault()
      params = $.url(e.currentTarget.href).segment();
      sole.offset = params[params.length-1];
      sole.navigate "search/" + sole.age_from + "/" + sole.age_to + "/" + sole.tag + "/" + sole.order_by + "/" + sole.offset, true
    
  # main router
  class Sole extends Backbone.Router
    
    # define all views in the sole
    user_list   : new UserList
    search_bar  : new Search
    pagination  : new Pagination
    head_earch  : new HeaderSearch
    
    # search params
    age_from      : ""
    age_to        : ""
    order_by      : "reputation"
    offset        : 0
    display_name  : ""
    tag           : ""
    
    # current view being displayed
    curr_user : null
    
    # what about routes?
    routes:
      "" : "search"
      "search/:age_from/:age_to/:tag/:order_by/:offset" : "search"
      "user/:display_name" : "user"
    
    # search the user
    user: (display_name) =>
      if display_name.length > 0
        @display_name = display_name
        @user_list.collection = null
        @user_list.display()
        $("input#display_name").val(@display_name)
      else
        @search()
    
    # the main search function
    search: (age_from, age_to, tag, order_by, offset) =>
      if !age_from
        @age_from = ''
      else
        @age_from = age_from
      
      if !age_to
        @age_to = ''
      else
        @age_to = age_to
        
      if !order_by
        @order_by = "reputation"
      else
        @order_by = order_by
        
      if !offset
        @offset = 0
      else
        @offset = offset
        
      if !tag
        @tag = ''
      else
        @tag = tag
      
      $("input#display_name").val("")
      @display_name = ""
      @search_bar.set_age()
      @search_bar.set_repute()
      @search_bar.set_tag()
      @user_list.collection = null
      @user_list.display()
    
  # instantiate new elseif app and start the history
  sole = new Sole
  Backbone.history.start()
  
  # add comma to the large number
  Handlebars.registerHelper 'format_num', (nStr) ->
    addCommas nStr
    
  addCommas = (nStr) ->
    nStr += ""
    x = nStr.split(".")
    x1 = x[0]
    x2 = (if x.length > 1 then "." + x[1] else "")
    rgx = /(\d+)(\d{3})/
    x1 = x1.replace(rgx, "$1" + "," + "$2")  while rgx.test(x1)
    x1 + x2
  
  #convert unix timestamp to date using momentjs
  Handlebars.registerHelper "convert_time", (obj) ->
    curr = moment(parseInt(obj) * 1000)
    curr.format("MMMM Do YYYY")

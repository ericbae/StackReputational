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
    
    initialize: =>
      @set_countries()
      $(@el).find(".option#state, .option#city").hide()
    
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
        
    set_countries : =>
      $.ajax
        url : base + "main/get_countries",
        dataType:"JSON",
        success: (data) =>
          $(@el).find("select#country").empty().append "<option value=''>All</option>"
          $.each data, (i, val) =>
            $(@el).find("select#country").append "<option value='" + val.country_long + "'>" + val.country_long + "</option>"
          $(@el).find("select#country").append "<option value='0'>UNKNOWN</option>"
          # if country is already set, let's also get the states
          if sole.country.length > 0
            $(@el).find("select#country").find("option[value='" + sole.country + "']").attr("SELECTED", true);
            @set_states()
    
    set_states : =>
      $.ajax
        url : base + "main/get_states",
        data : {country:sole.country}
        dataType:"JSON",
        success: (data) =>
          $(@el).find("select#state").empty().append "<option name=''>All</option>"
          $.each data, (i, val) =>
            $(@el).find("select#state").append "<option value='" + val.state_long + "'>" + val.state_long + "</option>"  
          $(@el).find(".option#state").fadeIn "fast"
          
          # if state is already set, let's also get the states
          if sole.state.length > 0
            $(@el).find("select#state").find("option[value='" + sole.state + "']").attr("SELECTED", true);
            @set_cities()
            
    set_cities : =>
       $.ajax
        url : base + "main/get_cities",
        data : {country:sole.country, state:sole.state}
        dataType:"JSON",
        success: (data) =>
          $(@el).find("select#city").empty().append "<option name=''>All</option>"
          $.each data, (i, val) =>
            $(@el).find("select#city").append "<option value='" + val.city_long + "'>" + val.city_long + "</option>"
          $(@el).find(".option#city").fadeIn "fast"
          
          # if state is already set, let's also get the states
          if sole.city.length > 0
            $(@el).find("select#city").find("option[value='" + sole.city + "']").attr("SELECTED", true);
        
    events :
      "change select#country"   : "search_country"
      "change select#state"     : "search_state"
      "change select#city"      : "search_city"
      "click .age_search"       : "search_age"
      "click .age_group"        : "search_age"
      "click .reset"            : "search_age"
      "click .repute"           : "search_repute"
      "click .tag"              : "search_tag"
      
    search_country : =>
      sole.country = $(@el).find("select#country").val()
      sole.state = ''
      sole.city = ''
      if sole.country.length > 0
        @set_states()
      else
        $(@el).find(".option#state").hide()
      $(@el).find(".option#city").hide()
      @perform_search()
      
    search_state : =>
      sole.state = $(@el).find("select#state").val()
      sole.city = ''
      @set_cities()
      @perform_search()
      
    search_city : =>
      sole.city = $(@el).find("select#city").val()
      @perform_search()
   
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
      sole.navigate "search/" + sole.country + "/" + sole.state + "/" + sole.city + "/" + sole.age_from + "/" + sole.age_to + "/" + sole.tag + "/" + sole.order_by + "/0", true

  # main user model
  class UserModel extends Backbone.Model
  
  # main user collection
  class UserCollection extends Backbone.Collection
    model: UserModel
    url  : base + "sole_api/users"
    fetch_users: (data, callback) => 
      @fetch
        data: data
        success: callback
  
  # main user view
  class UserItem extends Backbone.View
    className : "sixcol",
    template    :Handlebars.compile $("#user_hb").html()
    
    events :
      "click .user" : "view_more"
      
    view_more : =>
      if $(@el).find(".more_box").is(":visible")
        $(@el).find(".more_box").hide()
      else
        $(".more_box").hide()
        $(@el).find(".more_box").show()
        
        # make ajax request to show the user's claims, get it only if it's not there
        if $(@el).find(".fame.overall").text().length > 0
        else
          $(@el).find(".fame").hide()
          $(@el).find(".loading").show()
          $.ajax
            url:base + "main/user_fames"
            data:{user_id:@model.get "user_id"}
            dataType:"JSON"
            success: (data) =>
              $(@el).find(".loading").hide()
              $(@el).find(".fame").show()
              $(@el).find(".fame.overall").html("Overall rank : " + data.overall.rank)
              
              # country rank
              if data.country
                $(@el).find(".fame.country").html("National rank (in " + @model.get("country_long") + ") : " + data.country.rank)
              else
                $(@el).find(".fame.country").hide()
              
              if data.state  
                $(@el).find(".fame.state").html("State rank (in " + @model.get("state_long") + ") : " + data.state.rank)
              else
                $(@el).find(".fame.state").hide()
                
              if data.city
                $(@el).find(".fame.city").html("City rank (in " + @model.get("city_long") + ") : " + data.city.rank)
              else
                $(@el).find(".fame.city").hide()
                
              if data.age
                $(@el).find(".fame.age").html("Ranked " + data.age.rank + " among all " + @model.get("age") + "yr olds")
              else
                $(@el).find(".fame.age").hide()
                
              if data.age_group
                $(@el).find(".fame.age_group").html("Ranked " + data.age_group.rank + " in age group (" + (@model.get("age") - (@model.get("age") % 10) + 1) + "-" + (@model.get("age") - (@model.get("age") % 10) + 10) + ")")
              else
                $(@el).find(".fame.age_group").hide()
    
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
        country     :sole.country
        state       :sole.state
        city        :sole.city
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
      if sole.country.length > 0
        $("#param_box").find(".country").html("Country : " + sole.country);
      else
        $("#param_box").find(".country").html("Country : all");
      
      if sole.state.length > 0
        $("#param_box").find(".state").html("State : " + sole.state);
      else
        $("#param_box").find(".state").html("State : all");
      
      if sole.city.length > 0
        $("#param_box").find(".city").html("City : " + sole.city);
      else
        $("#param_box").find(".city").html("City : all");
      
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
        url : base + "main/get_num_users/" + sole.offset
        dataType:"JSON"
        data : 
          country     :sole.country
          state       :sole.state
          city        :sole.city
          age_from    :sole.age_from
          age_to      :sole.age_to
          display_name:sole.display_name
          tag         :sole.tag
          order_by    :sole.order_by
          offset      :sole.offset
        success: (data) =>
          $("#param_box").find(".size").html(addCommas(data.total) + " users found.")
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
      sole.navigate "search/" + sole.country + "/" + sole.state + "/" + sole.city + "/" + sole.age_from + "/" + sole.age_to + "/" + sole.tag + "/" + sole.order_by + "/" + sole.offset, true
    
  # main router
  class Sole extends Backbone.Router
    
    # define all views in the sole
    user_list   : new UserList
    search_bar  : new Search
    pagination  : new Pagination
    head_earch  : new HeaderSearch
    
    # search params
    country       : ""
    state         : ""
    city          : ""
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
      "search/:country/:state/:city/:age_from/:age_to/:tag/:order_by/:offset" : "search"
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
    search: (country, state, city, age_from, age_to, tag, order_by, offset) =>
      if !country or country is 'All'
        @country = ''
      else
        @country = country
      
      if !state or state is 'All'
        @state = ''
      else
        @state = state
      
      if !city or city is 'All'
         @city = ''
      else
        @city = city
        
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

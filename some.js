import {
    getAuth,
    getUser,
    callService,
    subscribeServices,
    createConnection,
    subscribeEntities,
    ERR_HASS_HOST_REQUIRED,
  } from "./dist/index.js";

  (async () => {
    
    let auth;
    try {
      auth = await getAuth();
      console.log(2)
    } catch (err) {
      if (err === ERR_HASS_HOST_REQUIRED) {
        const hassUrl ='http://192.168.1.15:8123';
        if (!hassUrl) return;
        auth = await getAuth({ hassUrl });
      } else {
        alert(`Unknown error: ${err}`);
        return;
      } 
    }
    
    const connection = await createConnection({ auth });
    subscribeEntities(connection, (entities) =>
     
      renderEntities(connection, entities)
    );
    subscribeEntities(connection, (entities) =>
     
      console.log(entities)
    );
    callService(connection,"climate",'set_temperature',{
      entity_id: "climate.study",
      temperature: 50
    })
    // Clear url if we have been able to establish a connection
    if (location.search.includes("auth_callback=1")) {
      history.replaceState(null, "", location.pathname);
    }

    // To play from the console
    window.auth = auth;
    window.connection = connection;
    getUser(connection).then((user) => {
      console.log("Logged in as", user);
      window.user = user;
    });
  })();
  
  
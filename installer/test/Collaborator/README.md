This is the directory where all the helper classes are put. 

Object Mothers all have a static only constructor to ease auto completion when writing tests. They are named after the class they create, and are suffixed:

- _Double_: Factory methods for all kinds of doubles. 
- _Actual_: Like the name implies, it is often just a getter method for the actual service in the container. It's also factory methods for fixtures.

## How To Use
1. Clone the D7 repo 
```
git clone <url_to_d7_codebase.git>
```
3. Add this to your D7 project composer.json file in the repositories section:
```
"dan612/d7_analyzer": {
    "type": "vcs",
    "url": "https://github.com/dan612/d7_analyzer.git"
}
```
3. Require the package so it's included in vendor dir
```
composer require dan612/d7_analyzer
```
4. Run the setup command  
  4a. copies default default.lando.yml to ./   
  4b. copies default.config.yml to ./  
  4c. asks for variables for config.yml   
  4d. runs lando start
```
./vendor/dan612/d7_analyzer/d7scan setup
```

Vagrant.configure("2") do |config|
  config.vm.box = "levelten/ubuntu64-php5.6"
  config.vm.box_version = "1.0.0"
  config.ssh.insert_key = false

  config.vm.network "forwarded_port", guest: 80, host: 8080	#apache / nginx
  config.vm.network "forwarded_port", guest: 3306, host: 3307	#mysql
end

Vagrant.configure(2) do |config|
  config.vm.box = "landregistry/centos"
  config.vm.network "public_network"
  config.vm.provision "shell", inline: <<-SHELL
     yum install -y epel-release curl php
     curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
  SHELL
end

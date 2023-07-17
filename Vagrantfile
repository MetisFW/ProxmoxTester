ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

Vagrant.configure("2") do |config|

  config.vm.define "proxmox-tester"
  config.vm.box = "skysilk/proxmox-pve7"
  config.vm.boot_timeout = 600


  config.vm.provider :virtualbox do |vb|
    vb.name = "proxmox-tester"
    vb.memory = 512
    vb.cpus = 1
  end

  config.vm.provision "shell",
    inline:  "sudo apt-get update && sudo apt-get install -y curl php7.4-cli php7.4-zip php7.4-curl php7.4-json php7.4-mbstring git unzip \
              && curl -sS https://getcomposer.org/installer | php \
              && sudo mv /home/vagrant/composer.phar /usr/local/bin/composer \
              && cd /vagrant && composer update \
              && bash tests/create.sh"

  config.vm.provision "shell",
    inline: 'echo "cd /vagrant/tests" >> /home/vagrant/.bashrc && cp -rp /home/vagrant/. /root/  && chown -R root /root'


end

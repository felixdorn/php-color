{ pkgs ? import <nixpkgs> { } }:

pkgs.mkShell
{
    nativeBuildInputs = with pkgs; let
        php =         (php83.buildEnv {
                          extraConfig = ''
                              memory_limit = 6G
                              xdebug.mode=coverage
                          '';

                          extensions = ({ enabled, all }: enabled ++ (with all; [
                              xdebug
                          ]));
                      });
     in [
        php
        php.packages.composer
    ];
}

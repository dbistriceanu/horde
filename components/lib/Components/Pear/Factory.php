<?php
/**
 * Components_Pear_Factory:: generates PEAR specific handlers.
 *
 * PHP version 5
 *
 * @category Horde
 * @package  Components
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Components
 */

/**
 * Components_Pear_Factory:: generates PEAR specific handlers.
 *
 * Copyright 2010-2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category Horde
 * @package  Components
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Components
 */
class Components_Pear_Factory
{
    /**
     * The dependency broker.
     *
     * @param Component_Dependencies
     */
    private $_dependencies;

    /**
     * Constructor.
     *
     * @param Component_Dependencies $dependencies The dependency broker.
     */
    public function __construct(Components_Dependencies $dependencies)
    {
        $this->_dependencies = $dependencies;
    }

    /**
     * Create a representation for a PEAR environment.
     *
     * @param string $environment The path to the PEAR environment.
     * @param string $config_file The path to the configuration file.
     *
     * @return Components_Pear_Environment The PEAR environment
     */
    public function createEnvironment($environment, $config_file)
    {
        $instance = $this->_dependencies->createInstance('Components_Pear_Environment');
        $instance->setFactory($this);
        $instance->setLocation(
            $environment,
            $config_file
        );
        return $instance;
    }

    /**
     * Create a package representation for a specific PEAR environment.
     *
     * @param string                          $package_file The path of the package XML file.
     * @param Components_Pear_Environment $environment  The PEAR environment.
     *
     * @return Components_Pear_Package The PEAR package.
     */
    public function createPackageForEnvironment(
        $package_file,
        Components_Pear_Environment $environment
    ) {
        $package = $this->_createPackage($environment);
        $package->setPackageXml($package_file);
        return $package;
    }

    /**
     * Create a package representation for a specific PEAR environment.
     *
     * @param string $package_file The path of the package XML file.
     * @param string $config_file  The path to the configuration file.
     *
     * @return Components_Pear_Package The PEAR package.
     */
    public function createPackageForPearConfig($package_file, $config_file)
    {
        return $this->createPackageForEnvironment(
            $package_file, $this->createEnvironment($config_file)
        );
    }

    /**
     * Create a package representation for the default PEAR environment.
     *
     * @param string $package_file The path of the package XML file.
     *
     * @return Components_Pear_Package The PEAR package.
     */
    public function createPackageForDefaultLocation($package_file)
    {
        return $this->createPackageForEnvironment(
            $package_file, $this->_dependencies->getInstance('Components_Pear_Environment')
        );
    }

    /**
     * Create a package representation for a specific PEAR environment based on a *.tgz archive.
     *
     * @param string                          $package_file The path of the package *.tgz file.
     * @param Components_Pear_Environment $environment  The environment for the package file.
     *
     * @return Components_Pear_Package The PEAR package.
     */
    public function createTgzPackageForEnvironment(
        $package_file,
        Components_Pear_Environment $environment
    ) {
        $package = $this->_createPackage($environment);
        $package->setPackageTgz($package_file);
        return $package;
    }

    /**
     * Create a generic package representation for a specific PEAR environment.
     *
     * @param Components_Pear_Environment $environment  The PEAR environment.
     *
     * @return Components_Pear_Package The generic PEAR package.
     */
    private function _createPackage(Components_Pear_Environment $environment)
    {
        $package = $this->_dependencies->createInstance('Components_Pear_Package');
        $package->setFactory($this);
        $package->setEnvironment($environment);
        return $package;
    }

    /**
     * Create a tree helper for a specific PEAR environment..
     *
     * @param string               $environment The path to the PEAR environment.
     * @param array                $options     The application options.
     * @param Components_Component $component   The component to work with. This
     *                                          is optional and only used to
     *                                          determine the root of the
     *                                          Horde repository.
     *
     * @return Components_Helper_Tree The tree helper.
     */
    public function createTreeHelper(
        $environment, array $options, Components_Component $component = null
    )
    {
        $instance = $this->_dependencies->createInstance('Components_Pear_Environment');
        $instance->setFactory($this);
        $instance->setLocation(
            $environment,
            $options['pearrc']
        );
        $instance->setResourceDirectories($options);
        return new Components_Helper_Tree(
            $this, $instance, new Components_Helper_Root($options, $component)
        );
    }

    /**
     * Create a tree helper for the default PEAR environment.
     *
     * @param array                $options     The application options
     * @param Components_Component $component   The component to work with. This
     *                                          is optional and only used to
     *                                          determine the root of the
     *                                          Horde repository.
     *
     * @return Components_Helper_Tree The tree helper.
     */
    public function createSimpleTreeHelper(
        array $options = array(), Components_Component $component
    )
    {
        return new Components_Helper_Tree(
            $this,
            $this->_dependencies->createInstance('Components_Pear_Environment'),
            new Components_Helper_Root($options, $component)
        );
    }

    /**
     * Return the PEAR Package representation.
     *
     * @param string                          $package_xml_path Path to the package.xml file.
     * @param Components_Pear_Environment $environment      The PEAR environment.
     *
     * @return PEAR_PackageFile
     */
    public function getPackageFile(
        $package_xml_path,
        Components_Pear_Environment $environment
    ) {
        $pkg = new PEAR_PackageFile($environment->getPearConfig());
        return Components_Exception_Pear::catchError(
            $pkg->fromPackageFile($package_xml_path, PEAR_VALIDATE_NORMAL)
        );
    }

    /**
     * Return the package.xml handler.
     *
     * @param string                          $package_xml_path Path to the package.xml file.
     *
     * @return Horde_Pear_Package_Xml
     */
    public function getPackageXml($package_xml_path)
    {
        return new Horde_Pear_Package_Xml($package_xml_path);
    }

    /**
     * Return the PEAR Package representation based on a local *.tgz archive.
     *
     * @param string                          $package_tgz_path Path to the *.tgz file.
     * @param Components_Pear_Environment $environment      The PEAR environment.
     *
     * @return PEAR_PackageFile
     */
    public function getPackageFileFromTgz(
        $package_tgz_path,
        Components_Pear_Environment $environment
    ) {
        $pkg = new PEAR_PackageFile($environment->getPearConfig());
        return Components_Exception_Pear::catchError(
            $pkg->fromTgzFile($package_tgz_path, PEAR_VALIDATE_NORMAL)
        );
    }

    /**
     * Create a new PEAR Package representation.
     *
     * @param string                          $package_xml_dir Path to the parent directory of the package.xml file.
     * @param Components_Pear_Environment $environment      The PEAR environment.
     *
     * @return PEAR_PackageFile
     */
    public function createPackageFile(
        $package_xml_dir
    ) {
        $type = new Horde_Pear_Package_Type_Horde($package_xml_dir);
        $type->writePackageXmlDraft();
    }

    /**
     * Create a package dependency helper.
     *
     * @param Components_Pear_Package $package The package.
     *
     * @return Components_Pear_Dependencies The dependency helper.
     */
    public function createDependencies(Components_Pear_Package $package)
    {
        $dependencies = $this->_dependencies->createInstance('Components_Pear_Dependencies');
        $dependencies->setPackage($package);
        return $dependencies;
    }
}
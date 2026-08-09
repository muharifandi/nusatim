/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : app
 * File : MainActivity.kt
 */

package com.nusatim.partner

import androidx.activity.OnBackPressedCallback
import androidx.activity.enableEdgeToEdge
import android.os.Bundle
import android.view.View
import androidx.core.net.toUri
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen

import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.NavOptions
import androidx.navigation.fragment.NavHostFragment
import androidx.navigation.ui.AppBarConfiguration
import androidx.navigation.ui.navigateUp
import androidx.navigation.ui.setupActionBarWithNavController
import androidx.navigation.ui.setupWithNavController
import com.nusatim.partner.core.architecture.base.BaseActivity
import com.nusatim.partner.core.common.navigation.NavigationCommand
import com.nusatim.partner.core.common.navigation.NavigationManager
import com.nusatim.partner.core.common.security.SecurityGuard
import com.nusatim.partner.databinding.ActivityMainBinding
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import timber.log.Timber
import javax.inject.Inject

@AndroidEntryPoint
class MainActivity : BaseActivity<ActivityMainBinding>() {

    @Inject
    lateinit var securityGuard: SecurityGuard

    @Inject
    lateinit var navigationManager: NavigationManager

    private lateinit var appBarConfiguration: AppBarConfiguration

    override fun onCreate(savedInstanceState: Bundle?) {
        installSplashScreen()
        enableEdgeToEdge()
        super.onCreate(savedInstanceState)
    }

    override fun onInitViews() {
        securityGuard.checkIntegrity(BuildConfig.DEBUG) { reason ->
            Timber.e("App terminated due to: $reason")
            finish()
        }

        setupNavigation()
        setupOnBackPressed()
        observeNavigation()
    }

    private fun observeNavigation() {
        val navHostFragment = supportFragmentManager.findFragmentById(R.id.nav_host_fragment) as NavHostFragment
        val navController = navHostFragment.navController

        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                navigationManager.navigationCommands.collect { command ->
                    when (command) {
                        is NavigationCommand.NavigateTo -> {
                            val route = command.route
                            if (route is String) {
                                navController.navigate(route.toUri())
                            }
                        }
                        is NavigationCommand.NavigateBack -> {
                            navController.popBackStack()
                        }
                        is NavigationCommand.NavigateAndPopUpTo -> {
                            val route = command.route
                            val popUpTo = command.popUpTo
                            if (route is String) {
                                val navOptions = NavOptions.Builder()
                                if (popUpTo is Int) {
                                    navOptions.setPopUpTo(popUpTo, command.inclusive)
                                }
                                navController.navigate(route.toUri(), navOptions.build())
                            }
                        }
                    }
                }
            }
        }
    }

    private fun setupOnBackPressed() {
        onBackPressedDispatcher.addCallback(
            this,
            object : OnBackPressedCallback(enabled = true) {
                override fun handleOnBackPressed() {
                    isEnabled = false
                    onBackPressedDispatcher.onBackPressed()
                    isEnabled = true
                }
            },
        )
    }

    private fun setupNavigation() {
        val navHostFragment = supportFragmentManager.findFragmentById(R.id.nav_host_fragment) as NavHostFragment
        val navController = navHostFragment.navController
        
        appBarConfiguration = AppBarConfiguration(
            setOf(
                R.id.navigation_home,
                R.id.navigation_leads,
                R.id.navigation_projects,
                R.id.navigation_finance,
                R.id.navigation_profile,
            )
        )

        binding.partnerBottomNav.bottomNav.setupWithNavController(navController)
        binding.partnerBottomNav.bottomNav.inflateMenu(R.menu.bottom_nav_menu)
        
        // setupActionBarWithNavController(navController, appBarConfiguration)

        navController.addOnDestinationChangedListener { _, destination, _ ->
            when (destination.id) {
                R.id.navigation_home,
                R.id.navigation_leads,
                R.id.navigation_projects,
                R.id.navigation_finance,
                R.id.navigation_profile,
                -> {
                    binding.partnerBottomNav.root.visibility = View.VISIBLE
                }
                else -> {
                    binding.partnerBottomNav.root.visibility = View.GONE
                }
            }
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        val navHostFragment = supportFragmentManager.findFragmentById(R.id.nav_host_fragment) as NavHostFragment
        val navController = navHostFragment.navController
        return navController.navigateUp(appBarConfiguration) || super.onSupportNavigateUp()
    }
}

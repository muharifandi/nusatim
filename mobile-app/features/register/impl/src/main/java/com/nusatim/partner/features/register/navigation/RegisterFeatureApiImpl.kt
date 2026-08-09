package com.nusatim.partner.features.register.navigation

import androidx.navigation.NavGraphBuilder
import androidx.navigation.NavHostController
import com.nusatim.partner.core.architecture.navigation.FeatureApi
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class RegisterFeatureApiImpl @Inject constructor() : FeatureApi {
    override fun registerGraph(
        navGraphBuilder: NavGraphBuilder,
        navController: NavHostController
    ) {
        // Implementation for registering register feature routes
        // For example:
        // navGraphBuilder.composable<RegisterDestinations> {
        //     RegisterScreen(navController)
        // }
    }
}

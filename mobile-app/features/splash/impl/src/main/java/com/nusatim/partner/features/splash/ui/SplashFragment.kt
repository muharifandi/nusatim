package com.nusatim.partner.features.splash.ui

import android.widget.Toast
import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.splash.databinding.FragmentSplashBinding
import com.nusatim.partner.features.splash.ui.state.SplashEffect
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class SplashFragment : BaseFragment<FragmentSplashBinding>() {

    private val viewModel: SplashViewModel by viewModels()

    override fun onInitViews() {
        // No views to initialize
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.effect.collect { effect ->
                    val navOptions = androidx.navigation.NavOptions.Builder()
                        .setPopUpTo(findNavController().currentDestination?.id ?: -1, true)
                        .build()

                    when (effect) {
                        is SplashEffect.NavigateToIntro -> {
                            findNavController().navigate("partner://intro".toUri(), navOptions)
                        }
                        is SplashEffect.NavigateToLogin -> {
                            findNavController().navigate("partner://login".toUri(), navOptions)
                        }
                        is SplashEffect.NavigateToHome -> {
                            findNavController().navigate("partner://home".toUri(), navOptions)
                        }
                        is SplashEffect.NavigateToApprovalStatus -> {
                            findNavController().navigate("partner://approval-status".toUri(), navOptions)
                        }
                        is SplashEffect.ShowError -> {
                            Toast.makeText(requireContext(), effect.message, Toast.LENGTH_LONG).show()
                        }
                    }
                }
            }
        }
    }
}

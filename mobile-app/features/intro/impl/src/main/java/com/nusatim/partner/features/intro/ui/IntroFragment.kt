package com.nusatim.partner.features.intro.ui

import androidx.core.net.toUri
import androidx.navigation.fragment.findNavController
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.tabs.TabLayoutMediator
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.intro.R
import com.nusatim.partner.features.intro.databinding.FragmentIntroBinding
import dagger.hilt.android.AndroidEntryPoint
import com.nusatim.partner.core.ui.R as CoreUiR

@AndroidEntryPoint
class IntroFragment : BaseFragment<FragmentIntroBinding>() {

    @javax.inject.Inject
    lateinit var sessionManager: com.nusatim.partner.core.common.security.SessionManager

    private lateinit var introAdapter: IntroAdapter

    override fun onInitViews() {
        setupViewPager()
        setupActions()
    }

    private fun setupViewPager() {
        val slides = listOf(
            IntroSlide(
                getString(R.string.intro_title_1),
                getString(R.string.intro_desc_1),
                CoreUiR.drawable.ic_onboarding_1
            ),
            IntroSlide(
                getString(R.string.intro_title_2),
                getString(R.string.intro_desc_2),
                CoreUiR.drawable.ic_onboarding_2
            ),
            IntroSlide(
                getString(R.string.intro_title_3),
                getString(R.string.intro_desc_3),
                CoreUiR.drawable.ic_onboarding_3
            ),
            IntroSlide(
                getString(R.string.intro_title_4),
                getString(R.string.intro_desc_4),
                CoreUiR.drawable.ic_illustration_success
            )
        )

        introAdapter = IntroAdapter(slides)
        binding.vpIntro.adapter = introAdapter

        TabLayoutMediator(binding.tlIndicator, binding.vpIntro) { _, _ -> }.attach()

        binding.vpIntro.registerOnPageChangeCallback(object : ViewPager2.OnPageChangeCallback() {
            override fun onPageSelected(position: Int) {
                super.onPageSelected(position)
                if (position == slides.size - 1) {
                    binding.btnNext.setText(R.string.btn_get_started)
                } else {
                    binding.btnNext.setText(R.string.btn_next)
                }
            }
        })
    }

    private fun setupActions() {
        binding.btnNext.setOnClickListener {
            if (binding.vpIntro.currentItem + 1 < introAdapter.itemCount) {
                binding.vpIntro.currentItem += 1
            } else {
                navigateToLogin()
            }
        }

        binding.tvSkip.setOnClickListener {
            navigateToLogin()
        }
    }

    private fun navigateToLogin() {
        sessionManager.setIntroCompleted(isCompleted = true)
        findNavController().navigate("partner://login".toUri())
    }

    override fun onInitObservers() {
        // No observers
    }
}

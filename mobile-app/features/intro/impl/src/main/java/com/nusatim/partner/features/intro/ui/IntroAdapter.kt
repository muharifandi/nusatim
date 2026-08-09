package com.nusatim.partner.features.intro.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.features.intro.databinding.ItemIntroSlideBinding

class IntroAdapter(private val slides: List<IntroSlide>) :
    RecyclerView.Adapter<IntroAdapter.IntroViewHolder>() {

    inner class IntroViewHolder(val binding: ItemIntroSlideBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(slide: IntroSlide) {
            binding.tvTitle.text = slide.title
            binding.tvDescription.text = slide.description
            binding.ivIntro.setImageResource(slide.imageRes)
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): IntroViewHolder {
        return IntroViewHolder(
            ItemIntroSlideBinding.inflate(
                LayoutInflater.from(parent.context),
                parent,
                false
            )
        )
    }

    override fun onBindViewHolder(holder: IntroViewHolder, position: Int) {
        holder.bind(slides[position])
    }

    override fun getItemCount(): Int = slides.size
}
